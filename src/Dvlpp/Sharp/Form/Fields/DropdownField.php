<?php

namespace Dvlpp\Sharp\Form\Fields;

/**
 * A simple select input element (dropdown).
 *
 * Class DropdownField
 * @package Dvlpp\Sharp\Form\Fields
 */
class DropdownField extends AbstractSharpField {

    /**
     * The actual HTML creation of the field.
     *
     * @return mixed
     */
    function make()
    {
        return $this->formBuilder()->select(
            $this->fieldName,
            $this->field->values(),
            $this->fieldValue,
            $this->attributes
        );
    }

} 