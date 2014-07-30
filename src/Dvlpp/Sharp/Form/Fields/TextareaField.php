<?php  namespace Dvlpp\Sharp\Form\Fields;

use Form;

class TextareaField extends AbstractSharpField {

    function make()
    {
        return Form::textarea($this->fieldName, $this->fieldValue, $this->attributes);
    }

} 