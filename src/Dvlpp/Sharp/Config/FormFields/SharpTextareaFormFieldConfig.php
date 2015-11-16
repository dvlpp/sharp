<?php

namespace Dvlpp\Sharp\Config\FormFields;

use Dvlpp\Sharp\Config\SharpFormFieldConfig;

class SharpTextareaFormFieldConfig extends SharpFormFieldConfig
{

    /**
     * @param string $key
     * @return static
     */
    public static function create($key)
    {
        $instance = new static;
        $instance->key = $key;

        $instance->label = "";

        return $instance;
    }

    public function type()
    {
        return "textarea";
    }
}