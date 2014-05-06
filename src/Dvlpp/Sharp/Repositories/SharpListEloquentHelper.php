<?php namespace Dvlpp\Sharp\Repositories;


use InvalidArgumentException;
use Str;

/**
 * Class SharpListEloquentHelper
 * @package Dvlpp\Sharp\Repositories
 *
 * This class can be used in repositories to manage the items update / create / delete of a Sharp list
 * while using Eloquent.
 */
class SharpListEloquentHelper {

    /**
     * This method updates all items of a list posted by a Sharp form.
     *
     * @param $listKey : the key of the list (ie: the name of the Sharp list as wall as the name of the model attribute)
     * @param $data : the posted data (Input)
     * @param $instance : the related Eloquent object, owner of the list
     * @param $repository : the project repository, which HAS TO IMPLEMENTS SharpListEloquentHelperInterface
     * @param null $orderAttribute : if provided, this attribute name will be updated with the item order
     * @throws \InvalidArgumentException
     */
    public static function updateList($listKey, $data, $instance, SharpListEloquentHelperInterface $repository, $orderAttribute=null)
    {
        // Get items posted
        $itemsForm = isset($data[$listKey]) ? $data[$listKey] : [];

        // Get the files attributes, ie names of attributes that are files.
        // We can find them navigating through the __file__$listKey array, which contains
        // full path of files that is used on repolulation case (after a validation error)
        $filesAttr = [];
        if(isset($data["__file__".$listKey]))
        {
            foreach($data["__file__".$listKey] as $nothing => $tabAttr)
            {
                foreach($tabAttr as $fileAttr => $nothing)
                {
                    $filesAttr[] = $fileAttr;
                }
            }
        }

        $order = 0;
        $saved = [];
        foreach($itemsForm as $itemForm)
        {
            $item = null;
            if(Str::startsWith($itemForm["id"], "N"))
            {
                // Have to create this item
                $item = $repository->createNewListItem($instance, $listKey);
            }
            else
            {
                foreach($instance->$listKey as $itemDb)
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
                // Item can't be found and isn't new. Error.
                throw new InvalidArgumentException("introuvable");
            }

            // Update item
            foreach($itemForm as $attr => $value)
            {
                if($attr == "id")
                {
                    // Id is not updatable
                    continue;
                }
                elseif(in_array($attr, $filesAttr))
                {
                    // This is a file attribute
                    $repository->updateListItemFileAttribute($item, $attr, $value, $listKey);
                }
                else
                {
                    // Normal attribute
                    $item->$attr = $value;
                }
            }

            // Manage order
            if($orderAttribute)
            {
                $item->$orderAttribute = $order;
            }

            // Eloquent save
            $item->save();

            // Keep reference of the item for deletions
            $saved[] = $item->id;

            $order++;
        }

        // Manage deletions of the non-present items
        foreach($instance->$listKey as $itemDb)
        {
            if(!in_array($itemDb->id, $saved))
            {
                $itemDb->delete();
            }
        }
    }
}