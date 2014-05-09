<?php namespace Dvlpp\Sharp\Form\Fields;

use Form;

class ChooseField extends AbstractSharpField {

    function make()
    {
        $values = $this->field->values;

        // And populate with formatted date the visible input field
        return Form::select($this->fieldName, $values, $this->fieldValue, $this->attributes);
    }

} 