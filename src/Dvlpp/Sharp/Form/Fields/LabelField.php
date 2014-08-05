<?php  namespace Dvlpp\Sharp\Form\Fields;

use Form;

class LabelField extends AbstractSharpField {

    function make()
    {
        $val = $this->fieldValue;

        if($this->field->format)
        {
            $val = $this->_format($val, $this->field->format);
        }

        return Form::label($this->fieldName, $val, $this->attributes);
    }

    private function _format($entity, $valToFormat)
    {
        $matches = [];
        preg_match_all('/%([^%]*)%/', $valToFormat, $matches, PREG_SET_ORDER);

        foreach($matches as $match)
        {
            $repl = $match[0];
            $string = $match[1];

            $value = $entity->{$string};

            $valToFormat = str_replace($repl, $value, $valToFormat);
        }

        return $valToFormat;
    }

} 