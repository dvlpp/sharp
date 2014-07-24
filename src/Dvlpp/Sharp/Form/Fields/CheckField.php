<?php namespace Dvlpp\Sharp\Form\Fields;

use Form;

class CheckField extends AbstractSharpField {

    function make()
    {
        // Put an hidden field with same name and 0 value in order to send it
        // in case of unchecked checkbox. Browser will choose the latest field.
        $str = Form::hidden($this->fieldName, 0);
        $str .= '<div class="checkbox"><label>';
        $str .= Form::checkbox($this->fieldName, 1, $this->field->checked);
        $str .= $this->field->text . '</label></div>';
        return $str;
    }

} 