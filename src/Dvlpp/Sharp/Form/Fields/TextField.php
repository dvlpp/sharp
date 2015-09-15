<?php namespace Dvlpp\Sharp\Form\Fields;

/**
 * A simple text input element.
 *
 * Class TextField
 * @package Dvlpp\Sharp\Form\Fields
 */
class TextField extends AbstractSharpField {

    /**
     * The actual HTML creation of the field.
     *
     * @return mixed
     */
    function make()
    {
        return $this->formBuilder()->text($this->fieldName, $this->fieldValue, $this->attributes);
    }

}