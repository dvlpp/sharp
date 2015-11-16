<?php

namespace Dvlpp\Sharp\Config\Utils;

trait HasFormTemplateTrait
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @param string $name
     * @return $this
     */
    public function addField($name)
    {
        $this->fields[] = $name;

        return $this;
    }

    /**
     * @param string $name
     * @param array $fieldNames
     * @return $this
     */
    public function addFieldset($name, $fieldNames)
    {
        $this->fields[$name] = $fieldNames;

        return $this;
    }

    /**
     * @return array
     */
    public function fields()
    {
        return $this->fields;
    }
}