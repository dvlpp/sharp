<?php namespace Dvlpp\Sharp\Repositories;

use Dvlpp\Sharp\Config\SharpCmsConfig;
use InvalidArgumentException;
use Str;
use DB;

/**
 * Class SharpEloquentRepositoryUpdaterTrait
 * @package Dvlpp\Sharp\Repositories
 */
trait SharpEloquentRepositoryUpdaterTrait {

    /**
     * @var
     */
    private $entityConfig;

    /**
     * Updates an entity with the posted data.
     *
     * @param $categoryName
     * @param $entityName
     * @param $instance
     * @param array $data
     * @return mixed
     */
    function updateEntity($categoryName, $entityName, $instance, Array $data)
    {
        // Start a transaction
        DB::connection()->getPdo()->beginTransaction();

        $this->entityConfig = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Iterate the posted data
        foreach($data as $dataAttribute => $dataAttributeValue)
        {
            foreach ($this->entityConfig->form_fields as $fieldId)
            {
                if ($fieldId == $dataAttribute)
                {
                    $fieldAttr = $this->entityConfig->form_fields->$fieldId;

                    $this->updateField($instance, $data, $fieldAttr, $dataAttribute);
                }
            }
        }

        $instance->save();

        DB::connection()->getPdo()->commit();

        return $instance;
    }

    /**
     * @param $instance
     * @param $pivotKey
     * @param $dataPivot
     * @param $pivotConfig
     */
    private function valuatePivotAttribute($instance, $pivotKey, $dataPivot, $pivotConfig)
    {
        $isCreatable = $pivotConfig->addable ?: false;
        $createAttribute = $isCreatable ? $pivotConfig->create_attribute : "name";
        $hasOrder = $pivotConfig->sortable ?: false;
        $orderAttribute = $hasOrder ? $pivotConfig->order_attribute : "order";

        $existingPivots = [];
        $newPivots = [];
        $order = 1;
        foreach($dataPivot as $d)
        {
            if(!starts_with($d, '#'))
            {
                // Existing tag
                if($hasOrder)
                {
                    $existingPivots[$d] = [$orderAttribute=>$order++];
                }
                else
                {
                    $existingPivots[] = $d;
                }
            }

            elseif($isCreatable)
            {
                // Create a new tag
                $newPivots[$order++] = substr($d, 1);
            }
        }

        // Sync existing ones
        $instance->$pivotKey()->sync($existingPivots);

        // Create new
        foreach($newPivots as $order => $newPivot)
        {
            $joiningArray = $orderAttribute ? [$orderAttribute=>$order] : [];

            $instance->$pivotKey()->create([$createAttribute => $newPivot], $joiningArray);
        }
    }

    /**
     * @param $instance
     * @param $attr
     * @param $value
     * @param bool $isDate
     */
    private function valuateSimpleAttribute($instance, $attr, $value, $isDate=false)
    {
        $instance->$attr = $this->getFieldValue($value, $isDate);
    }

    /**
     * @param $instance
     * @param $attr
     * @param $file
     */
    private function valuateFileAttribute($instance, $attr, $file)
    {
        if($file && $file != $instance->$attr)
        {
            // Update (or create)

            // First, we move the file in the correct folder
            $folderPath = $this->getFileUploadPath($instance, $attr);
            sharp_move_tmp_file($file, $folderPath);

            // Then, update database
            $this->updateFileUpload($instance, $attr, $file);
        }

        elseif(!$file && $instance->$attr)
        {
            // Delete
            $this->deleteFileUpload($instance, $attr);
        }
    }

    /**
     * @param $instance
     * @param $listKey
     * @param $itemsForm
     * @param $listFieldConfig
     * @throws \InvalidArgumentException
     */
    private function valuateListAttribute($instance, $listKey, $itemsForm, $listFieldConfig)
    {
        $order = 0;
        $saved = [];

        if(is_array($itemsForm))
        {
            $itemIdAttribute = $listFieldConfig->item_id_attribute ?: "id";

            // Iterate items posted
            foreach($itemsForm as $itemForm)
            {
                $item = null;
                $itemId = $itemForm[$itemIdAttribute];

                if(Str::startsWith($itemId, "N_"))
                {
                    // First test if there is a special hook method on the controller
                    // that takes the precedence. Method name should be "create[$listKey]ListItem"
                    $methodName = "create" . ucFirst(Str::camel($listKey)) . "ListItem";

                    if(method_exists($this, $methodName))
                    {
                        $item = $this->$methodName($instance);
                    }
                    else
                    {
                        // Have to create this item : we can't use $entity->$listKey()->create([]), because
                        // we don't want a ->save() call on the item (which could fail because of mandatory DB attribute)
                        $item = $instance->$listKey()->getRelated()->newInstance([]);
                        $item->setAttribute($instance->$listKey()->getPlainForeignKey(), $instance->$listKey()->getParentKey());
                    }
                }
                else
                {
                    foreach($instance->$listKey as $itemDb)
                    {
                        if($itemDb->$itemIdAttribute == $itemId)
                        {
                            // DB item found
                            $item = $itemDb;
                            break;
                        }
                    }
                }

                if(!$item)
                {
                    // Item can't be found and isn't new. It's an error.
                    throw new InvalidArgumentException("Item [$itemId] can't be found.");
                }

                // Update item
                foreach($itemForm as $attr => $value)
                {
                    if($attr == $itemIdAttribute)
                    {
                        // Id is not updatable
                        continue;
                    }

                    // For other attributes :
                    foreach ($listFieldConfig->item as $configListItemKey)
                    {
                        if ($configListItemKey == $attr)
                        {
                            $configListItemConfigAttr = $listFieldConfig->item->$configListItemKey;

                            $this->updateField($item, $itemForm, $configListItemConfigAttr, $configListItemKey, $listKey);
                        }
                    }
                }

                // Manage order
                if($listFieldConfig->order_attribute)
                {
                    $item->{$listFieldConfig->order_attribute} = $order;
                }

                // Eloquent save
                $item->save();

                // Keep reference of the item for deletions
                $saved[] = $item->$itemIdAttribute;

                $order++;
            }
        }

        // Manage deletions of the non-present items
        foreach($instance->$listKey as $itemDb)
        {
            if(!in_array($itemDb->$itemIdAttribute, $saved))
            {
                $itemDb->delete();
            }
        }
    }

    /**
     * @param $value
     * @param $isDate
     * @return bool|string
     */
    private function getFieldValue($value, $isDate)
    {
        return $isDate ? date("Y-m-d H:i:s", strtotime($value)) : $value;
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
    private function updateField($instance, $data, $configFieldAttr, $dataAttribute, $listKey=null)
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

        if(method_exists($this, $methodName))
        {
            // Method exists, we call it
            if(!$this->$methodName($instance, $data[$dataAttribute]))
            {
                // Returns false: we are done with this attribute.
                return;
            }
        }

        // Otherwise, we have to manage this attribute ourselves...

        $value = $data[$dataAttribute];

        // These vars are used to store old values of $dataAttribute and $entity
        // in case of modification by singleRelationCase (below)
        $baseEntity = null;
        $baseAttribute = null;

        $isSingleRelationCase = strpos($dataAttribute, "~");

        if($isSingleRelationCase)
        {
            // If there's a "~" in the field $key, this means we are in a single relation case
            // (One-To-One or Belongs To). The ~ separate the relation name and the value.
            // For instance : boss~name indicate that the instance as a single "boss" relation,
            // which has a "name" attribute.
            list($relationKey, $relationAttribute) = explode("~", $dataAttribute);

            $relationObject = $instance->$relationKey;

            if(!$relationObject)
            {
                // Related object has to be created.

                // First persist entity if transient
                if(!$instance->id) $instance->save();

                // Then create the related object
                $relationObject = $instance->$relationKey()->getRelated()->newInstance([]);
                $relationObject->setAttribute($instance->$relationKey()->getPlainForeignKey(), $instance->$relationKey()->getParentKey());

                // Unset the relation to be sure that other attribute of the same relation will
                // use the created related object (otherwise, relation is cached to null by Laravel)
                unset($instance->$relationKey);
            }

            // Finally, we translate entity and attribute to the related object
            $baseEntity = $instance;
            $baseAttribute = $dataAttribute;
            $dataAttribute = $relationAttribute;
            $instance = $relationObject;
        }

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
                $this->valuateSimpleAttribute($instance, $dataAttribute, $value);
                break;

            case "date":
                $this->valuateSimpleAttribute($instance, $dataAttribute, $value, true);
                break;

            case "file":
                $this->valuateFileAttribute($instance, $dataAttribute, $value);
                break;

            case "list":
                // First save the entity if transient (item creation would be impossible if entity is not persisted)
                if(!$instance->id) $instance->save();

                // Find list config
                $listFieldConfig = null;
                foreach ($this->entityConfig->form_fields as $fieldId)
                {
                    if ($fieldId == ($baseAttribute ?: $dataAttribute))
                    {
                        $listFieldConfig = $this->entityConfig->form_fields->$fieldId;
                        break;
                    }
                }

                $this->valuateListAttribute($instance, $dataAttribute, $value, $listFieldConfig);
                break;

            case "pivot":
                // First save the entity if transient (pivot creation would be impossible if entity is not persisted)
                if(!$instance->id) $instance->save();

                // Find pivot config
                $pivotConfig = null;
                foreach ($this->entityConfig->form_fields as $fieldId)
                {
                    if ($fieldId == ($baseAttribute ?: $dataAttribute))
                    {
                        $pivotConfig = $this->entityConfig->form_fields->$fieldId;
                        break;
                    }
                }

                $this->valuatePivotAttribute($instance, $dataAttribute, $value, $pivotConfig);
                break;

            default:
                throw new InvalidArgumentException("Config type [".$configFieldAttr->type."] is invalid.");
        }

        if($isSingleRelationCase && $configFieldAttr->type != "list")
        {
            // In a single relation case, we have to save the eloquent relation,
            // except if it's a list (saving occurs on items)
            $baseEntity->$relationKey()->save($relationObject);
        }
    }

} 