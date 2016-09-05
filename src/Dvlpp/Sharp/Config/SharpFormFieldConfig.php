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
     * @var SharpEntityConfig
     */
    protected $entity;

    /**
     * @var string
     */
    protected $formatter;

    /**
     * @var string
     */
    protected $valuator;

    /**
     * @var bool
     */
    protected $readonly;

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
        if(is_array($this->conditionalDisplayValues)) {
            $values = implode(",", $this->conditionalDisplayValues);
            return $this->conditionalDisplayField . ":" . $values;
        }

        if(!$this->conditionalDisplayValues) {
            return '!' . $this->conditionalDisplayField;
        }

        return $this->conditionalDisplayField;
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

    /**
     * @param SharpEntityConfig $entity
     */
    public function setEntity(SharpEntityConfig $entity)
    {
        $this->entity = $entity;
    }

    public function entity()
    {
        return $this->entity;
    }

    /**
     * @param string $formatter
     * @return $this
     */
    public function setFormatter($formatter)
    {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * @return string
     */
    public function formatter()
    {
        return $this->formatter;
    }

    /**
     * @param string $valuator
     * @return $this
     */
    public function setValuator($valuator)
    {
        $this->valuator = $valuator;

        return $this;
    }

    /**
     * @return string
     */
    public function valuator()
    {
        return $this->valuator;
    }

    public function setReadOnly($readonly = true)
    {
        $this->readonly = $readonly;

        return $this;
    }

    public function readOnly()
    {
        return $this->readonly;
    }
}