<?php

namespace Dvlpp\Sharp\Config;

/**
 * Base class for form fields config.
 *
 * Class SharpFormFieldConfig
 * @package Dvlpp\Sharp\Config
 */
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
     * @var string
     */
    protected $helpMessage;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var string
     */
    protected $conditionalDisplayField;

    /**
     * @var bool|array
     */
    protected $conditionalDisplayValues;

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

    /**
     * @return string
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function label()
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function conditionalDisplayField()
    {
        return $this->conditionalDisplayField;
    }

    /**
     * @return array|bool
     */
    public function conditionalDisplayValues()
    {
        return $this->conditionalDisplayValues;
    }

    public function isConditionalDisplay()
    {
        return !is_null($this->conditionalDisplayField);
    }

    abstract public function type();

    /**
     * @return string
     */
    public function helpMessage()
    {
        return $this->helpMessage;
    }

    /**
     * @param string $helpMessage
     * @return $this
     */
    public function setHelpMessage($helpMessage)
    {
        $this->helpMessage = $helpMessage;

        return $this;
    }
}