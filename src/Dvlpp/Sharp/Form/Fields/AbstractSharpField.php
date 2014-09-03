<?php namespace Dvlpp\Sharp\Form\Fields;

use Dvlpp\Sharp\Config\Entities\SharpEntityFormField;
use Dvlpp\Sharp\Exceptions\MandatoryEntityAttributeNotFoundException;
use Input;

/**
 * This this all Sharp's fields superclass.
 *
 * Class AbstractSharpField
 * @package Dvlpp\Sharp\Form\Fields
 */
abstract class AbstractSharpField {

    /**
     * @var string
     */
    protected $key;
    /**
     * @var \Dvlpp\Sharp\Config\Entities\SharpEntityFormField
     */
    protected $field;
    /**
     * @var string
     */
    protected $fieldName;
    /**
     * @var array
     */
    protected $attributes;
    /**
     * @var
     */
    protected $instance;
    /**
     * @var string
     */
    protected $fieldValue;
    /**
     * @var string
     */
    protected $listKey;
    /**
     * @var bool
     */
    protected $isListItem = false;
    /**
     * @var string
     */
    protected $relation;
    /**
     * @var string
     */
    protected $relationKey;

    /**
     * Construct the field.
     *
     * @param $key
     * @param $listKey
     * @param SharpEntityFormField $field
     * @param $attributes
     * @param $instance
     */
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

    /**
     * Add a class name to the class attribute.
     *
     * @param $className
     */
    protected function addClass($className)
    {
        $this->attributes["class"] = $className . (array_key_exists("class", $this->attributes) ? " ".$this->attributes["class"] : "");
    }

    /**
     * Add a data-XXX attribute
     *
     * @param $name
     * @param $data
     */
    protected function addData($name, $data)
    {
        $this->attributes["data-$name"] = $data;
    }

    /**
     * Check for missing mandatory attributes.
     *
     * @param array $attributes
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryEntityAttributeNotFoundException
     */
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
     *
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

    /**
     * The actual HTML creation of the field.
     *
     * @return mixed
     */
    abstract function make();
}