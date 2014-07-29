<?php namespace Dvlpp\Sharp\Form\Fields;

use Form;

class CheckField extends AbstractSharpField {

    function make()
    {
        // Put an hidden field with same name and 0 value in order to send it
        // in case of unchecked checkbox. Browser will choose the latest field.
        $str = Form::hidden($this->fieldName, 0);

        // And manage the checkbox itself
        $str .= '<div class="checkbox"><label>';

        // For the checkbox value, we take the instance value, and fallback with an optional config default "checked"
        $str .= Form::checkbox($this->fieldName, 1, $this->instance ? $this->fieldValue : $this->field->checked);
        $str .= $this->field->text . '</label></div>';

        return $str;
    }

} 