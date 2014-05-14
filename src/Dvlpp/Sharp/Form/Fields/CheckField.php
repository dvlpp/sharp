<?php namespace Dvlpp\Sharp\Form\Fields;

use Form;

class CheckField extends AbstractSharpField {

    function make()
    {
        $str = '<div class="checkbox"><label>';
        $str .= Form::hidden($this->fieldName, 0);
        $str .= Form::checkbox($this->fieldName, 1, $this->field->checked);
        $str .= $this->field->text . '</label></div>';
        return $str;
    }

} 