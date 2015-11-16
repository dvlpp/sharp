<?php

namespace Dvlpp\Sharp\Config\FormFields;

use Dvlpp\Sharp\Config\SharpFormFieldConfig;

/**
 * Config for a check field.
 *
 * Class SharpCheckFormFieldConfig
 * @package Dvlpp\Sharp\Config\FormFields
 */
class SharpCheckFormFieldConfig extends SharpFormFieldConfig
{
    /**
     * @var bool
     */
    protected $checked;

    /**
     * @var string
     */
    protected $text;

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

    /**
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function type()
    {
        return "check";
    }

    /**
     * @return string
     */
    public function text()
    {
        return $this->text;
    }

    /**
     * @return boolean
     */
    public function checked()
    {
        return $this->checked;
    }


}