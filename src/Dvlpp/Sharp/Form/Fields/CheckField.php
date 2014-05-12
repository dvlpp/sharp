<?php namespace Dvlpp\Sharp\Form\Fields;

use Form;

class CheckField extends AbstractSharpField {

    function make()
    {
        $str = '<div class="checkbox"><label>';
        $str .= Form::checkbox($this->fieldName, $this->fieldValue, $this->field->checked);
        $str .= $this->field->text . '</label></div>';
        return $str;
    }

} 