<?php

namespace Dvlpp\Sharp\Config\FormFields;

use Dvlpp\Sharp\Config\SharpFormFieldConfig;

class SharpRefFormFieldConfig extends SharpFormFieldConfig
{
    /**
     * @var string
     */
    protected $repository;

    /**
     * @var bool
     */
    protected $creatable;

    /**
     * @param string $key
     * @param string $repository
     * @return static
     */
    public static function create($key, $repository)
    {
        $instance = new static;
        $instance->key = $key;
        $instance->repository = $repository;

        $instance->label = "";
        $instance->creatable = false;

        return $instance;
    }

    public function type()
    {
        return "ref";
    }

    /**
     * @return string
     */
    public function repository()
    {
        return $this->repository;
    }

    /**
     * @param bool $create
     * @return $this
     */
    public function setCreatable($create)
    {
        $this->creatable = $create;

        return $this;
    }

    /**
     * @return bool
     */
    public function creatable()
    {
        return $this->creatable;
    }
}