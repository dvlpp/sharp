<?php namespace Dvlpp\Sharp\AdvancedSearch\Fields;

use Input;

abstract class AdvancedSearchAbstractField {

    protected $key;
    protected $field;
    protected $attributes;
    protected $value;

    function __construct($key, $field, $attributes)
    {
        $this->key = $key;
        $this->field = $field;
        $this->attributes = $attributes;
        $this->value = Input::get($key) ?: null;
    }

    public abstract function make();

    protected function addClass($className)
    {
        $this->attributes["class"] = $className . (array_key_exists("class", $this->attributes) ? " ".$this->attributes["class"] : "");
    }

} 