<?php namespace Dvlpp\Sharp\Repositories;


use Dvlpp\Sharp\Config\SharpCmsConfig;
use InvalidArgumentException;
use Str;
use DB;

trait SharpEloquentRepositoryUpdaterTrait {

    private $entityConfig;

    function updateEntity($categoryName, $entityName, $entity, Array $data)
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

                    $this->updateField($entity, $data, $fieldAttr, $dataAttribute);
                }
            }
        }

        $entity->save();

        DB::connection()->getPdo()->commit();

        return $entity;
    }

    private function valuatePivotAttribute($entity, $pivotKey, $dataPivot, $pivotConfig)
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
            if(is_numeric($d))
            {
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
                $newPivots[$order++] = $d;
            }
        }

        // Sync existing ones
        $entity->$pivotKey()->sync($existingPivots);

        // Create new
        foreach($newPivots as $order => $newPivot)
        {
            $joiningArray = $orderAttribute ? [$orderAttribute=>$order] : [];

            $entity->$pivotKey()->create([$createAttribute => $newPivot], $joiningArray);
        }
    }

    private function valuateSimpleAttribute($entity, $attr, $value, $isDate=false)
    {
        $entity->$attr = $this->getFieldValue($value, $isDate);
    }

    private function valuateFileAttribute($entity, $attr, $file)
    {
        if($file && $file != $entity->$attr)
        {
            // Update (or create)

            // First, we move the file in the correct folder
            $folderPath = $this->getFileUploadPath($entity, $attr);
            sharp_move_tmp_file($file, $folderPath);

            // Then, update database
            $this->updateFileUpload($entity, $attr, $file);
        }

        elseif(!$file && $entity->$attr)
        {
            // Delete
            $this->deleteFileUpload($entity, $attr);
        }
    }

    private function valuateListAttribute($entity, $listKey, $itemsForm, $listFieldConfig)
    {
        $order = 0;
        $saved = [];

        if(is_array($itemsForm))
        {
            // Iterate items posted
            foreach($itemsForm as $itemForm)
            {
                $item = null;
                if(Str::startsWith($itemForm["id"], "N"))
                {
                    // Have to create this item : we can't use $entity->$listKey()->create([]), because
                    // we don't want a ->save() call on the item (which could fail because of mandatory DB attribute)
                    $item = $entity->$listKey()->getRelated()->newInstance([]);
                    $item->setAttribute($entity->$listKey()->getPlainForeignKey(), $entity->$listKey()->getParentKey());
                }
                else
                {
                    foreach($entity->$listKey as $itemDb)
                    {
                        if($itemDb->id == $itemForm["id"])
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
                    throw new InvalidArgumentException("Item [".$itemForm["id"]."] can't be found.");
                }

                // Update item
                foreach($itemForm as $attr => $value)
                {
                    if($attr == "id")
                    {
                        // Id is not updatable
                        continue;
                    }

                    //if($listKey == "tarifsSpecifiques") dd($entity);

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
                $saved[] = $item->id;

                $order++;
            }
        }

        // Manage deletions of the non-present items
        foreach($entity->$listKey as $itemDb)
        {
            if(!in_array($itemDb->id, $saved))
            {
                $itemDb->delete();
            }
        }
    }

    private function getFieldValue($value, $isDate)
    {
        return $isDate ? date("Y-m-d H:i:s", strtotime($value)) : $value;
    }

    /**
     * Updates the field value (in db).
     *
     * @param $entity
     * @param $data
     * @param $configFieldAttr
     * @param $dataAttribute
     * @param null $listKey
     * @throws \InvalidArgumentException
     */
    private function updateField($entity, $data, $configFieldAttr, $dataAttribute, $listKey=null)
    {
        // First test if there is a special hook method on the controller
        // that takes the precedence. Method name should be :
        // "update[$dataAttribute]Attribute" for a normal field
        // "update[$listKey]List[$dataAttribute]Attribute" for an list item field.
        // For example : updateBooksListTitleAttribute
        $methodName = "update"
            . ($listKey ? ucFirst(Str::camel($listKey)) . "List" : "")
            . ucFirst(Str::camel($dataAttribute))
            . "Attribute";

        if(method_exists($this, $methodName))
        {
            // Method exists, we call it
            if(!$this->$methodName($entity, $data[$dataAttribute]))
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

            $relationObject = $entity->$relationKey;

            if(!$relationObject)
            {
                // Related object has to be created.

                // First persist entity if transient
                if(!$entity->id) $entity->save();

                // Then create the related object
                $relationObject = $entity->$relationKey()->getRelated()->newInstance([]);
                $relationObject->setAttribute($entity->$relationKey()->getPlainForeignKey(), $entity->$relationKey()->getParentKey());

                // Unset the relation to be sure that other attribute of the same relation will
                // use the created related object (otherwise, relation is cached to null by Laravel)
                unset($entity->$relationKey);
            }

            // Finally, we translate entity and attribute to the related object
            $baseEntity = $entity;
            $baseAttribute = $dataAttribute;
            $dataAttribute = $relationAttribute;
            $entity = $relationObject;
        }

        switch ($configFieldAttr->type)
        {
            case "text":
            case "ref":
            case "check":
            case "choose":
            case "textarea":
            case "password":
            case "markdown":
                $this->valuateSimpleAttribute($entity, $dataAttribute, $value);
                break;

            case "date":
                $this->valuateSimpleAttribute($entity, $dataAttribute, $value, true);
                break;

            case "file":
                $this->valuateFileAttribute($entity, $dataAttribute, $value);
                break;

            case "list":
                // First save the entity if transient (item creation would be impossible if entity is not persisted)
                if(!$entity->id) $entity->save();

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

                $this->valuateListAttribute($entity, $dataAttribute, $value, $listFieldConfig);
                break;

            case "pivot":
                // First save the entity if transient (pivot creation would be impossible if entity is not persisted)
                if(!$entity->id) $entity->save();

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

                $this->valuatePivotAttribute($entity, $dataAttribute, $value, $pivotConfig);
                break;

            default:
                throw new InvalidArgumentException("Config type [".$configFieldAttr->type."] is invalid.");
        }

        if($isSingleRelationCase)
        {
            $baseEntity->$relationKey()->save($relationObject);
        }
    }

} 