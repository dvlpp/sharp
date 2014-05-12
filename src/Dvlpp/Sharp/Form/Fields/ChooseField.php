<?php namespace Dvlpp\Sharp\Form\Fields;

use Form;

class ChooseField extends AbstractSharpField {

    function make()
    {
        return Form::select($this->fieldName, $this->field->values, $this->fieldValue, $this->attributes);
    }

} 