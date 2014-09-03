<?php namespace Dvlpp\Sharp\Form\Fields;

use Form;

/**
 * A password input field.
 *
 * Class PasswordField
 * @package Dvlpp\Sharp\Form\Fields
 */
class PasswordField extends AbstractSharpField {

    /**
     * The actual HTML creation of the field.
     *
     * @return mixed
     */
    function make()
    {
        return Form::password($this->fieldName, $this->attributes);
    }

} 