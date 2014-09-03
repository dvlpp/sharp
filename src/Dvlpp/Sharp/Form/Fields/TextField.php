<?php namespace Dvlpp\Sharp\Form\Fields;

use Form;

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
        return Form::text($this->fieldName, $this->fieldValue, $this->attributes);
    }

}