<?php

namespace Dvlpp\Sharp\Config\FormFields;

use Dvlpp\Sharp\Config\SharpFormFieldConfig;

class SharpLabelFormFieldConfig extends SharpFormFieldConfig
{
    protected $format;

    protected $style = "";

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

    public function addStyle($style)
    {
        $this->style = $style;

        return $this;
    }

    public function format()
    {
        return $this->format;
    }

    public function style()
    {
        return $this->style;
    }
}