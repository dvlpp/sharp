<?php  namespace Dvlpp\Sharp\Form\Fields;

use Form;

class HiddenField extends AbstractSharpField {

    function make()
    {
        return Form::hidden($this->fieldName, $this->fieldValue, $this->attributes);
    }

} 