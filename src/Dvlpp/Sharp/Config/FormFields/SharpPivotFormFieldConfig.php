<?php

namespace Dvlpp\Sharp\Config\FormFields;

use Dvlpp\Sharp\Config\SharpFormFieldConfig;

class SharpPivotFormFieldConfig extends SharpFormFieldConfig
{
    /**
     * @var string
     */
    protected $handler;

    /**
     * @var bool
     */
    protected $addable;

    /**
     * @var bool
     */
    protected $sortable;

    /**
     * @var string
     */
    protected $orderAttribute;

    /**
     * @var string
     */
    protected $createAttribute;

    /**
     * @param string $key
     * @param string $handler
     * @return static
     */
    public static function create($key, $handler)
    {
        $instance = new static;
        $instance->key = $key;
        $instance->handler = $handler;
        $instance->addable = false;
        $instance->sortable = false;

        $instance->label = "";

        return $instance;
    }

    /**
     * @param bool $addable
     * @return $this
     */
    public function setAddable($addable)
    {
        $this->addable = $addable;

        return $this;
    }

    /**
     * @param bool $sortable
     * @return $this
     */
    public function setSortable($sortable)
    {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * @param string $orderAttribute
     * @return $this
     */
    public function setOrderAttribute($orderAttribute)
    {
        $this->orderAttribute = $orderAttribute;

        return $this;
    }

    /**
     * @param string $createAttribute
     * @return $this
     */
    public function setCreateAttribute($createAttribute)
    {
        $this->createAttribute = $createAttribute;

        return $this;
    }
}