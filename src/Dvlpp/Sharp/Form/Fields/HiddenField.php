<?php namespace Dvlpp\Sharp\Form\Fields;

/**
 * A simple hidden input element.
 *
 * Class HiddenField
 * @package Dvlpp\Sharp\Form\Fields
 */
class HiddenField extends AbstractSharpField {

    /**
     * The actual HTML creation of the field.
     *
     * @return mixed
     */
    function make()
    {
        return $this->formBuilder()->hidden($this->fieldName, $this->fieldValue, $this->attributes);
    }

} 