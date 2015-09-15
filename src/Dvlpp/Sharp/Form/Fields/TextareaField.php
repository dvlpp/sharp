<?php namespace Dvlpp\Sharp\Form\Fields;

/**
 * A simple textarea input element.
 *
 * Class TextareaField
 * @package Dvlpp\Sharp\Form\Fields
 */
class TextareaField extends AbstractSharpField {

    /**
     * The actual HTML creation of the field.
     *
     * @return mixed
     */
    function make()
    {
        return $this->formBuilder()->textarea($this->fieldName, $this->fieldValue, $this->attributes);
    }

} 