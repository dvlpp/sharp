<?php

namespace Dvlpp\Sharp\Config\FormFields;

use Dvlpp\Sharp\Config\SharpFormFieldConfig;

class SharpDropdownFormFieldConfig extends SharpFormFieldConfig
{
    /**
     * @var array
     */
    protected $values;

    /**
     * @param string $key
     * @param array $values
     * @return static
     */
    public static function create($key, $values)
    {
        $instance = new static;
        $instance->key = $key;
        $instance->values = $values;

        $instance->label = "";

        return $instance;
    }

}