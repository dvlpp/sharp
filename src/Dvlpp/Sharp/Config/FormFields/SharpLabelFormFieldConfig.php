<?php

namespace Dvlpp\Sharp\Config\FormFields;

use Dvlpp\Sharp\Config\SharpFormFieldConfig;

class SharpLabelFormFieldConfig extends SharpFormFieldConfig
{
    protected $format;

    /**
     * @param string $key
     * @param string $format
     * @return static
     */
    public static function create($key, $format)
    {
        $instance = new static;
        $instance->key = $key;
        $instance->format = $format;

        $instance->label = "";

        return $instance;
    }

    public function type()
    {
        return "label";
    }

    public function format()
    {
        return $this->format;
    }
}