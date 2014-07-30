<?php  namespace Dvlpp\Sharp\Form\Fields;

use Form;

class TextField extends AbstractSharpField {

    function make()
    {
        return Form::text($this->fieldName, $this->fieldValue, $this->attributes);
    }

} 