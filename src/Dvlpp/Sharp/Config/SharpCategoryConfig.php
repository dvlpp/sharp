<?php

namespace Dvlpp\Sharp\Config;

abstract class SharpCategoryConfig
{
    /**
     * The displayed label.
     * @var string
     */
    protected $label = "";

    /**
     * Array of entity keys.
     * @var array
     */
    protected $entities = [];

    /**
     * @var string
     */
    private $key;

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function label()
    {
        return $this->label;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function entities()
    {
        return collect($this->entities);
    }

    /**
     * @return string
     */
    public function key()
    {
        return $this->key;
    }
}