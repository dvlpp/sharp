<?php

namespace Dvlpp\Sharp\Config;

use Dvlpp\Sharp\Config\Utils\HasFormTemplateTrait;
use Dvlpp\Sharp\Config\Utils\SharpFormTemplate;

/**
 * Template of a form Column.
 *
 * Class SharpFormTemplateColumnConfig
 * @package Dvlpp\Sharp\Config
 */
class SharpFormTemplateColumnConfig implements SharpFormTemplate
{
    use HasFormTemplateTrait;

    /**
     * @var int
     */
    protected $width;

    /**
     * @param int $width
     * @return static
     */
    public static function create($width=6)
    {
        $instance = new static;
        $instance->width = $width;

        return $instance;
    }

    /**
     * @return int
     */
    public function width()
    {
        return $this->width;
    }
}