<?php namespace Dvlpp\Sharp\Form\Fields;

use Dvlpp\Sharp\Form\Facades\SharpCmsField;
use Form;
use Input;
use Lang;

/**
 * An ordered list of items containing fields.
 *
 * Class ListField
 * @package Dvlpp\Sharp\Form\Fields
 */
class ListField extends AbstractSharpField {

    /**
     * The actual HTML creation of the field.
     *
     * @return string
     */
    function make()
    {
        // Manage data attributes
        $strAttr = "";
        if($this->field->addable) $strAttr .= 'data-addable="'.$this->field->addable.'"';
        if($this->field->removable) $strAttr .= ' data-removable="'.$this->field->removable.'"';
        if($this->field->sortable) $strAttr .= ' data-sortable="'.$this->field->sortable.'"';
        if($this->field->add_button_text) $strAttr .= ' data-add_button_text="'.e($this->field->add_button_text).'"';

        // Add this hidden to send the list with nothing in case of 0 item.
        // It's useful to post the empty list and be able to delete all
        // potentially existing items.
        $str = '<input type="hidden" name="'.$this->key.'" value="empty">';

        $str .= '<ul class="sharp-list list-group" '.$strAttr.'>';

        $listkey = $this->key;
        if(Input::old($listkey))
        {
            // Form is re-displayed (validation errors): have to grab old values instead of DB
            if(is_array(Input::old($listkey)))
            {
                foreach(Input::old($listkey) as $item)
                {
                    $str .= $this->createItem((object)$item);
                }
            }
        }
        else
        {
            $collection = $this->relation
                ? ($this->instance && $this->instance->{$this->relation} ? $this->instance->{$this->relation}->{$this->relationKey} : [])
                : $this->instance->$listkey;

            foreach($collection as $item)
            {
                $str .= $this->createItem($item);
            }
        }

        if($this->field->addable)
        {
            $str .= $this->createTemplate();
        }

        $str .= '</ul>';

        return $str;
    }

    /**
     * @return string
     */
    private function createTemplate()
    {
        return $this->createItem(null);
    }

    /**
     * @param $item
     * @return string
     */
    private function createItem($item)
    {
        $itemIdAttribute = $this->field->item_id_attribute ?: "id";

        $isTemplate = ($item === null);

        $hiddenKey = $this->key."[".($isTemplate?"--N--":$item->id)."][$itemIdAttribute]";

        $strItem = '<li class="list-group-item sharp-list-item '.($isTemplate?"template":"").'"><div class="row">'
            . Form::hidden($hiddenKey, $isTemplate?"N":$item->$itemIdAttribute, ["class"=>"sharp-list-item-id"]);

        foreach($this->field->item as $key)
        {
            $itemField = $this->field->item->$key;

            $strField = '<div class="col-md-' . ($itemField->field_width ?: "12") . '">'
                . '<div class="form-group sharp-field sharp-field-' . $itemField->type . '"'
                . ($itemField->conditional_display ? ' data-conditional_display='.$itemField->conditional_display : '')
                .'>' . SharpCmsField::make($key, $itemField, $item, $this->key)
                . '</div></div>';

            $strItem .= $strField;
        }

        if($this->field->removable)
        {
            $strRemove = $this->field->remove_button_text ?: Lang::get('sharp::ui.form_listField_deleteItem');
            $strItem .= '<div class="col-md-12"><a class="sharp-list-remove btn btn-sm"><i class="fa fa-times"></i> '.$strRemove.'</a></div>';
        }

        $strItem .= '</div></li>';

        return $strItem;
    }
} 