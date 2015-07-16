<?php namespace Dvlpp\Sharp\AdvancedSearch\Fields;

use Form;

class AdvancedSearchTextField extends AdvancedSearchAbstractField {

    function make()
    {
        return Form::text($this->key, $this->value, $this->attributes);
    }

} 