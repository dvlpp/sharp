<?php  namespace Dvlpp\Sharp\Form\Fields;

use Form;

class PasswordField extends AbstractSharpField {

    function make()
    {
        return Form::password($this->fieldName, $this->attributes);
    }

} 