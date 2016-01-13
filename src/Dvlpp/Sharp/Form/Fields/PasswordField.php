<?php

namespace Dvlpp\Sharp\Form\Fields;

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
        return $this->formBuilder()->password($this->fieldName, $this->attributes);
    }

} 