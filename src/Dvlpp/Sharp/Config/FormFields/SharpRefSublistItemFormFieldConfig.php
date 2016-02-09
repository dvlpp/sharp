<?php

namespace Dvlpp\Sharp\Config\FormFields;

use Dvlpp\Sharp\Config\SharpFormFieldConfig;

class SharpRefSublistItemFormFieldConfig extends SharpFormFieldConfig
{
    /**
     * @var string
     */
    protected $repository;

    /**
     * @var string
     */
    protected $linkedRefField;

    /**
     * @var string
     */
    protected $refListKey;

    /**
     * @param string $key
     * @param string $repository
     * @param string $linkedRefField
     * @param string $refListKey
     * @return static
     */
    public static function create($key, $repository, $linkedRefField, $refListKey)
    {
        $instance = new static;
        $instance->key = $key;
        $instance->repository = $repository;
        $instance->linkedRefField = $linkedRefField;
        $instance->refListKey = $refListKey;

        $instance->label = "";

        return $instance;
    }

    public function type()
    {
        return "refSublistItem";
    }

    /**
     * @return string
     */
    public function repository()
    {
        return $this->repository;
    }

    /**
     * @return string
     */
    public function linkedRefField()
    {
        return $this->linkedRefField;
    }

    /**
     * @return string
     */
    public function refListKey()
    {
        return $this->refListKey;
    }

}