<?php namespace Dvlpp\Sharp\Form\Fields;


use Dvlpp\Sharp\Config\Entities\SharpEntityFormField;
use Dvlpp\Sharp\Exceptions\MandatoryEntityAttributeNotFoundException;
use Input;

abstract class AbstractSharpField {

    protected $key;
    protected $field;
    protected $fieldName;
    protected $attributes;
    protected $instance;
    protected $fieldValue;
    protected $listKey;
    protected $isListItem = false;
    protected $relation;
    protected $relationKey;

    function __construct($key, $listKey, SharpEntityFormField $field, $attributes, $instance)
    {
        $this->key = $key;
        $this->field = $field;
        $this->attributes = $attributes;
        $this->instance = $instance;

        $this->fieldName = $listKey ? $listKey."[".($instance?$instance->id:"--N--")."][".$key."]" : $key;

        $this->relation = null;
        $this->relationKey = null;

        if(strpos($key, "~"))
        {
            // If there's a "~" in the field $key, this means we are in a single relation case
            // (One-To-One or Belongs To). The ~ separate the relation name and the value.
            // For instance : boss~name indicate that the instance as a single "boss" relation,
            // which has a "name" attribute.
            list($this->relation, $this->relationKey) = explode("~", $key);
            $this->fieldValue = $instance && $instance->{$this->relation} ? $instance->{$this->relation}->{$this->relationKey} : null;
        }
        else
        {
            $this->fieldValue = $instance ? $instance->$key : null;
        }

        if($listKey)
        {
            $this->listKey = $listKey;
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

    /**
     * Retrieve the old value (Input::old) if it exists.
     * @return null
     */
    protected function getOldValue()
    {
        // If no instance (template for example), no need to go further
        if(!$this->instance) return null;

        if($this->isListItem)
        {
            // If is list item, have to look inside list array
            $list = Input::old($this->listKey);
            $item = $list[$this->instance->id];
            return $item[$this->key];
        }
        else
        {
            return Input::old($this->key);
        }
    }
}