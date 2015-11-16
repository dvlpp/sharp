<?php

namespace Dvlpp\Sharp\Config;

use Dvlpp\Sharp\Config\Utils\HasFormTemplateColumnTrait;

class SharpFormTemplateTabConfig
{
    use HasFormTemplateColumnTrait;

    /**
     * @var string
     */
    protected $label;

    /**
     * @param string $label
     * @return static
     */
    public static function create($label)
    {
        $instance = new static;
        $instance->label = $label;

        return $instance;
    }

    /**
     * @return string
     */
    public function label()
    {
        return $this->label;
    }

}