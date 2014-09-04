<?php namespace Dvlpp\Sharp\Form\Fields;

use Form;

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
        // Put an hidden field with same name and 0 value in order to send it
        // in case of unchecked checkbox. Browser will choose the latest field.
        // We can't use Form::hidden because we *don't want* repopulation here
        $str = '<input type="hidden" name="'.$this->fieldName.'" value="0">';

        // And manage the checkbox itself
        $str .= '<div class="checkbox"><label>';

        // For the checkbox value, we take the instance value, and fallback with an optional config default "checked"
        $str .= Form::checkbox($this->fieldName, 1, $this->field->checked);
        $str .= $this->field->text . '</label></div>';

        return $str;
    }

} 