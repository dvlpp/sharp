<?php namespace Dvlpp\Sharp\Form\Fields;

/**
 * A checkbox input element.
 *
 * Class CheckField
 * @package Dvlpp\Sharp\Form\Fields
 */
class CheckField extends AbstractSharpField {

    /**
     * The actual HTML creation of the field.
     *
     * @return string
     */
    function make()
    {
        $this->_checkMandatoryAttributes(["text"]);

        // For the checkbox value, we take the instance value, and fallback with an optional config default "checked"
        $value = $this->fieldValue !== null ? $this->fieldValue : $this->field->checked;

        // Put an hidden field with same name and 0 value in order to send it
        // in case of unchecked checkbox. Browser will choose the latest field.
        // We can't use Form::hidden because we *don't want* repopulation here
        return '<input type="hidden" name="'.$this->fieldName.'" value="0">'
            . '<div class="checkbox"><label>'
            . $this->formBuilder()->checkbox($this->fieldName, 1, $value)
            . $this->field->text
            . '</label></div>';
    }

} 