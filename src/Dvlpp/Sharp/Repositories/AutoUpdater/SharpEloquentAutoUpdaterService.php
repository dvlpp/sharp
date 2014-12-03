<?php namespace Dvlpp\Sharp\Repositories\AutoUpdater;

use Dvlpp\Sharp\Config\SharpCmsConfig;
use Dvlpp\Sharp\Repositories\AutoUpdater\Valuators\DateValuator;
use Dvlpp\Sharp\Repositories\AutoUpdater\Valuators\EmbedListValuator;
use Dvlpp\Sharp\Repositories\AutoUpdater\Valuators\EmbedValuator;
use Dvlpp\Sharp\Repositories\AutoUpdater\Valuators\ListValuator;
use Dvlpp\Sharp\Repositories\AutoUpdater\Valuators\PivotTagValuator;
use Dvlpp\Sharp\Repositories\AutoUpdater\Valuators\FileValuator;
use Dvlpp\Sharp\Repositories\AutoUpdater\Valuators\SimpleValuator;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Class SharpEloquentAutoUpdaterService
 * @package Dvlpp\Sharp\Repositories\AutoUpdater
 */
class SharpEloquentAutoUpdaterService {

    /**
     * @var
     */
    private $entityConfig;

    /**
     * @var SharpCmsRepository
     */
    private $sharpRepository;

    /**
     * @var array
     */
    private $singleRelationOneToOneObjects = [];

    /**
     * @var array
     */
    private $singleRelationBelongsToObjects = [];

    /**
     * Updates an entity with the posted data.
     *
     * @param SharpCmsRepository $sharpRepository
     * @param $categoryName
     * @param $entityName
     * @param $instance
     * @param array $data
     * @return mixed
     */
    function updateEntity(SharpCmsRepository $sharpRepository, $categoryName, $entityName, $instance, Array $data)
    {
        $entityConfig = SharpCmsConfig::findEntity($categoryName, $entityName);

        return $this->updateEntityWithConfig($sharpRepository, $entityConfig, $instance, $data);
    }

    /**
     * Updates an entity with the posted data.
     *
     * @param SharpCmsRepository $sharpRepository
     * @param $entityConfig
     * @param $instance
     * @param array $data
     * @param null $callbackBeforeSave
     * @return mixed
     */
    function updateEntityWithConfig(SharpCmsRepository $sharpRepository, $entityConfig, $instance, Array $data, $callbackBeforeSave=null)
    {
        $this->sharpRepository = $sharpRepository;
        $this->entityConfig = $entityConfig;

        // Iterate the posted data
        foreach($data as $dataAttribute => $dataAttributeValue)
        {
            foreach ($this->entityConfig->form_fields as $fieldKey)
            {
                if ($fieldKey == $dataAttribute)
                {
                    $fieldAttr = $this->entityConfig->form_fields->$fieldKey;

                    $this->updateField($instance, $data, $fieldAttr, $dataAttribute);

                    break;
                }
            }
        }

        // First manage (potential) single relation objects with a "BelongsTo" relation
        foreach($this->singleRelationBelongsToObjects as $relationKey => $singleRelationBelongsTo)
        {
            // Persist the related object...
            $singleRelationBelongsTo->save();
            // And then attach the foreign key
            $lk = $instance->$relationKey()->getForeignKey();
            $fk = $instance->$relationKey()->getOtherKey();
            $instance->$lk = $singleRelationBelongsTo->$fk;
        }

        if($callbackBeforeSave)
        {
            // A "before save" callback was registered
            call_user_func($callbackBeforeSave, $instance);
        }

        // Then save the actual instance
        $instance->save();

        // And finally manage (potential) single relation objects with a "OneToOne" or "morphTo" relation
        foreach($this->singleRelationOneToOneObjects as $relationKey => $singleRelationOneToOne)
        {
            $instance->$relationKey()->save($singleRelationOneToOne);
        }

        return $instance;
    }

    /**
     * Updates the field value (in db).
     *
     * @param $instance
     * @param $data
     * @param $configFieldAttr
     * @param $dataAttribute
     * @param null $listKey
     * @throws \InvalidArgumentException
     */
    public function updateField($instance, $data, $configFieldAttr, $dataAttribute, $listKey=null)
    {
        // First test if there is a special hook method on the controller
        // that takes the precedence. Method name should be :
        // "update[$dataAttribute]Attribute" for a normal field
        // "update[$listKey]List[$dataAttribute]Attribute" for an list item field.
        // For example : updateBooksListTitleAttribute
        $methodName = "update"
            . ($listKey ? ucFirst(Str::camel($listKey)) . "List" : "")
            . ucFirst(Str::camel( str_replace("~", "_", $dataAttribute)))
            . "Attribute";

        if(method_exists($this->sharpRepository, $methodName))
        {
            // Method exists, we call it
            if(!$this->sharpRepository->$methodName($instance, $data[$dataAttribute]))
            {
                // Returns false: we are done with this attribute.
                return;
            }
        }

        // Otherwise, we have to manage this attribute ourselves...

        $value = $data[$dataAttribute];

        // These vars are used to store old values of $dataAttribute and $entity
        // in case of modification by singleRelationCase (below)
        $baseInstance = null;
        $baseAttribute = null;

        $isSingleRelationCase = strpos($dataAttribute, "~");

        if($isSingleRelationCase)
        {
            // If there's a "~" in the field $key, this means we are in a single relation case
            // (One-To-One or Belongs To). The ~ separates the relation name and the value.
            // For instance : boss~name indicate that the instance as a single "boss" relation,
            // which has a "name" attribute.
            list($relationKey, $relationAttribute) = explode("~", $dataAttribute);

            $relationObject = $instance->$relationKey;

            if(!$relationObject)
            {
                if(array_key_exists($relationKey, $this->singleRelationOneToOneObjects))
                {
                    $relationObject = $this->singleRelationOneToOneObjects[$relationKey];
                }
                elseif(array_key_exists($relationKey, $this->singleRelationBelongsToObjects))
                {
                    $relationObject = $this->singleRelationBelongsToObjects[$relationKey];
                }
            }

            if(!$relationObject)
            {
                // Related object has to be created.

                // If value is null, we won't create the related instance
                if(!$value) return;

                // We create the related object
                $relationObject = $instance->$relationKey()->getRelated()->newInstance([]);

                // Unset the relation to be sure that other attribute of the same relation will
                // use the created related object (otherwise, relation is cached to null by Laravel)
                unset($instance->$relationKey);
            }

            // Then, we have to save the related object in order to persist it at the end of the update process
            // We can't save it right now because of potential mandatory attributes which will be treated later
            // in the process, and for the OneToOne case because we can't be sure that the current instance
            // has an ID to provide
//            if($configFieldAttr->type != "list")
//            {
            if($instance->$relationKey() instanceof BelongsTo)
            {
                // BelongsTo: foreign key is on the instance object side
                $this->singleRelationBelongsToObjects[$relationKey] = $relationObject;
            }
            else
            {
                // One-to-one or morphOne: foreign key is on the related object side
                $this->singleRelationOneToOneObjects[$relationKey] = $relationObject;
            }
//            }

            // Finally, we translate attributes to the related object
            $baseAttribute = $dataAttribute;
            $dataAttribute = $relationAttribute;
            $instance = $relationObject;
        }

        $valuator = null;

        switch ($configFieldAttr->type)
        {
            case "text":
            case "ref":
            case "refSublistItem":
            case "check":
            case "choose":
            case "textarea":
            case "password":
            case "markdown":
                $valuator = new SimpleValuator($instance, $dataAttribute, $value);
                break;

            case "date":
                $valuator = new DateValuator($instance, $dataAttribute, $value);
                break;

            case "file":
                $cropValues = isset($data["__filecrop__" . $dataAttribute]) ? $data["__filecrop__" . $dataAttribute] : null;
                $valuator = new FileValuator($instance, $dataAttribute, $value, $this->sharpRepository, $cropValues);
                break;

            case "list":
                // Find list config
                $listFieldConfig = $this->findListConfig($baseAttribute ?: $dataAttribute);
                $valuator = new ListValuator($instance, $dataAttribute, $value, $listFieldConfig, $this->sharpRepository, $this);
                break;

            case "pivot":
                // Find pivot config
                $pivotConfig = $this->findPivotConfig($baseAttribute ? : $dataAttribute, $listKey);
                $valuator = new PivotTagValuator($instance, $dataAttribute, $value, $pivotConfig, $this->sharpRepository);
                break;

            case "embed":
                $embedConfig = SharpCmsConfig::findEntity($configFieldAttr->entity_category, $configFieldAttr->entity);
                $valuator = new EmbedValuator($instance, $dataAttribute, $value, $embedConfig, $this->sharpRepository);
                break;

            case "embed_list":
                $embedConfig = SharpCmsConfig::findEntity($configFieldAttr->entity_category, $configFieldAttr->entity);
                $valuator = new EmbedListValuator($instance, $dataAttribute, $value, $configFieldAttr, $embedConfig, $this->sharpRepository);
                break;

            case "label":
                // Nothing to do...
                return;

            default:
                throw new InvalidArgumentException("Config type [".$configFieldAttr->type."] is invalid.");
        }

        $valuator->valuate();

    }

    /**
     * @param $dataAttribute
     * @return mixed
     */
    private function findListConfig($dataAttribute)
    {
        $listFieldConfig = null;

        foreach ($this->entityConfig->form_fields as $fieldId)
        {
            if ($fieldId == $dataAttribute)
            {
                $listFieldConfig = $this->entityConfig->form_fields->$fieldId;
                break;
            }
        }

        return $listFieldConfig;
    }

    /**
     * @param $dataAttribute
     * @param $listKey
     * @return null
     */
    private function findPivotConfig($dataAttribute, $listKey)
    {
        if ($listKey)
        {
            // It's in a list item
            $listConfig = $this->findListConfig($listKey);
            $fields = $listConfig->item;
        }
        else
        {
            $fields = $this->entityConfig->form_fields;
        }

        foreach ($fields as $fieldId)
        {
            if ($fieldId == $dataAttribute)
            {
                return $fields->$fieldId;
            }
        }

        return null;
    }
} 