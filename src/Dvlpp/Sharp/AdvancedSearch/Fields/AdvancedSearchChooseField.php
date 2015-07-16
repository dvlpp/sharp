<?php namespace Dvlpp\Sharp\AdvancedSearch\Fields;

use Form;

class AdvancedSearchChooseField extends AdvancedSearchAbstractField {

    function make()
    {
        return Form::select($this->key, $this->field->values, $this->value, $this->attributes);
    }

} 