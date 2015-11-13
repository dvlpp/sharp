<?php

namespace Dvlpp\Sharp\Config;

abstract class SharpFormFieldConfig
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param string $fieldName
     * @param bool|array $values
     * @return $this
     */
    public function setConditionalDisplay($fieldName, $values=true)
    {
        $this->conditionalDisplayField = $fieldName;
        $this->conditionalDisplayValues = $values;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }
}