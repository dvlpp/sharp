<?php

namespace Dvlpp\Sharp\Config;

/**
 * Config for a column in the entities list.
 *
 * Class SharpListTemplateColumnConfig
 * @package Dvlpp\Sharp\Config
 */
class SharpListTemplateColumnConfig
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $headingLabel;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var string
     */
    protected $renderer;

    /**
     * @var bool
     */
    protected $sortable;

    /**
     * Create a new instance.
     *
     * @param string $key
     * @return static
     */
    public static function create($key)
    {
        $instance = new static;
        $instance->key = $key;

        $instance->headingLabel = "";
        $instance->size = 2;
        $instance->sortable = false;

        return $instance;
    }

    /**
     * @param string $heading
     * @return $this
     */
    public function setHeading($heading)
    {
        $this->headingLabel = $heading;

        return $this;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @param string $columnRenderer
     * @return $this
     */
    public function setColumnRenderer($columnRenderer)
    {
        $this->renderer = $columnRenderer;

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
}