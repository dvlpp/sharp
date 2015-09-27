<?php

namespace Dvlpp\Sharp\Config\Entities;

use Dvlpp\Sharp\Exceptions\MandatoryEntityAttributeNotFoundException;

/**
 * Class HasProperties
 * @package Dvlpp\Sharp\Entities
 *
 * Base class for all structured objects from the entities configuration.
 *
 */
abstract class HasProperties
{

    /**
     * Properties for which a MandatoryEntityAttributeNotFoundException is thrown at parse
     * if not found in the config file.
     * @var array
     */
    protected $mandatoryProperties = [];

    /**
     * Define a default value for properties
     * @var array
     */
    protected $defaultPropertiesValues = [];

    /**
     * Properties for which a dedicated class is instantiated
     * @var array
     */
    protected $structProperties = [];

    /**
     * data from the config file
     * @var array
     */
    protected $data = [];

    /**
     * @var
     */
    protected $parent;


    /**
     * @param $data
     * @param $parent
     */
    public function __construct($data, $parent)
    {
        if (!is_array($data) && starts_with($data, "file:")) {
            // Case $data is a simple string: it refers to an external Config file
            // We load it and gets its data
            $data = config()->get(substr($data, strlen("file:")));
        }

        $this->data = $data;

        $this->parent = $parent;
    }


    /**
     * @param $attribute
     * @return null
     */
    public function __get($attribute)
    {
        if (!isset($this->$attribute)) {
            if (array_key_exists("__ALL__", $this->structProperties)
                || array_key_exists($attribute, $this->structProperties)
            ) {
                $className = $this->structProperties[array_key_exists("__ALL__",
                    $this->structProperties) ? "__ALL__" : $attribute];
                $struct = new $className(isset($this->data[$attribute]) ? $this->data[$attribute] : [], $this);
                $this->$attribute = $struct;

            } else {
                $this->$attribute = $this->getEntitySimpleProperty(
                    $attribute,
                    in_array($attribute, $this->mandatoryProperties),
                    array_key_exists($attribute, $this->defaultPropertiesValues)
                        ? $this->defaultPropertiesValues[$attribute]
                        : null
                );
            }
        }

        return $this->$attribute;
    }

    /**
     * @param $name
     * @param bool $mandatory
     * @param null $defaultValue
     * @return null
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryEntityAttributeNotFoundException
     */
    private function getEntitySimpleProperty($name, $mandatory = false, $defaultValue = null)
    {
        $val = array_key_exists($name, $this->data) ? $this->data[$name] : null;
        if (!$val && $mandatory) {
            throw new MandatoryEntityAttributeNotFoundException("Mandatory attribute [$name] not found.");
        }

        return $val !== null ? $val : $defaultValue;
    }

    /**
     * Get the key of the entity which owns the field
     *
     * @return null|string
     */
    public function getEntityKey()
    {
        $parent = $this->parent;
        while ($parent && !$parent instanceof SharpEntity) {
            $parent = $parent->parent;
        }

        return $parent ? $parent->key : null;
    }

    /**
     * Get the key of the category which owns the field
     *
     * @return null|string
     */
    public function getCategoryKey()
    {
        $parent = $this->parent;

        while ($parent && !$parent instanceof SharpCategory) {
            $parent = $parent->parent;
        }

        return $parent ? $parent->key : null;
    }
} 