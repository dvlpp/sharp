<?php

namespace Dvlpp\Sharp\Config\Commands;

use Dvlpp\Sharp\Config\SharpFormTemplate;

class SharpCommandFormTemplateConfig implements SharpFormTemplate
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @return static
     */
    public static function create()
    {
        return new static;
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