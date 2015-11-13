<?php

namespace Dvlpp\Sharp\Config;

class SharpFormTemplateColumnConfig
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var int
     */
    protected $width;

    /**
     * @param int $width
     * @return static
     */
    public static function create($width=6)
    {
        $instance = new static;
        $instance->width = $width;

        return $instance;
    }

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
        $this->fields[] = [
            $name => $fieldNames
        ];

        return $this;
    }
}