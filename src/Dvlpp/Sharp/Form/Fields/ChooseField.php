<?php namespace Dvlpp\Sharp\Form\Fields;

use Form;

/**
 * A simple select input element (dropdown).
 *
 * Class ChooseField
 * @package Dvlpp\Sharp\Form\Fields
 */
class ChooseField extends AbstractSharpField {

    /**
     * The actual HTML creation of the field.
     *
     * @return mixed
     */
    function make()
    {
        return Form::select($this->fieldName, $this->field->values, $this->fieldValue, $this->attributes);
    }

} 