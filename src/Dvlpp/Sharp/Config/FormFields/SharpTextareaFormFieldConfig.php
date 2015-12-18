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

    /**
     * @return string
     */
    public function type()
    {
        return "textarea";
    }

    /**
     * @param int $rowsCount
     * @return $this
     */
    public function setRows($rowsCount)
    {
        $this->addAttribute("rows", $rowsCount);

        return $this;
    }
}