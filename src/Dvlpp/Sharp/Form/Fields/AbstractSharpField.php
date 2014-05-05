<?php namespace Dvlpp\Sharp\Form\Fields;


use Dvlpp\Sharp\Config\Entities\SharpEntityFormField;
use Dvlpp\Sharp\Exceptions\MandatoryEntityAttributeNotFoundException;

abstract class AbstractSharpField {

    protected $key;
    protected $field;
    protected $fieldName;
    protected $attributes;
    protected $instance;
    protected $fieldValue;
    protected $isListItem = false;

    function __construct($key, $listKey, SharpEntityFormField $field, $attributes, $instance)
    {
        $this->key = $key;
        $this->field = $field;
        $this->attributes = $attributes;
        $this->instance = $instance;

        $this->fieldName = $listKey ? $listKey."[".($instance?$instance->id:"--N--")."][".$key."]" : $key;
        $this->fieldValue = $instance ? $instance->$key : null;

        if($listKey)
        {
            $this->isListItem = true;
        }
    }

    protected function addClass($className)
    {
        $this->attributes["class"] = $className . (array_key_exists("class", $this->attributes) ? " ".$this->attributes["class"] : "");
    }

    protected function addData($name, $data)
    {
        $this->attributes["data-$name"] = $data;
    }

    protected function _checkMandatoryAttributes(Array $attributes)
    {
        foreach($attributes as $attr)
        {
            if($this->field->$attr === null)
            {
                throw new MandatoryEntityAttributeNotFoundException("Attribute [$attr] can't be found (Field: ".$this->key.")");
            }
        }
    }
}