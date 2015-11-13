<?php

namespace Dvlpp\Sharp\Config\FormFields;

use Dvlpp\Sharp\Config\SharpFormFieldConfig;

class SharpMarkdownFormFieldConfig extends SharpFormFieldConfig
{

    /**
     * @var bool
     */
    protected $toolbar;

    /**
     * @var int
     */
    protected $height;

    public static function create($key)
    {
        $instance = new static;
        $instance->key = $key;

        $instance->label = "";
        $instance->toolbar = true;
        $instance->height = 300;

        return $instance;
    }

    /**
     * @param bool $toolbar
     * @return $this
     */
    public function showToolbar($toolbar)
    {
        $this->toolbar = $toolbar;

        return $this;
    }

    /**
     * @param int $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }
}