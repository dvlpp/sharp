<?php

namespace Dvlpp\Sharp\Config\FormFields;

use Dvlpp\Sharp\Config\SharpFormFieldConfig;

class SharpCheckFormFieldConfig extends SharpFormFieldConfig
{
    /**
     * @var bool
     */
    protected $checked;

    public static function create($key)
    {
        $instance = new static;
        $instance->key = $key;

        $instance->label = "";
        $instance->checked = false;

        return $instance;
    }

    /**
     * @param bool $checked
     * @return $this
     */
    public function setChecked($checked)
    {
        $this->checked = $checked;

        return $this;
    }

}