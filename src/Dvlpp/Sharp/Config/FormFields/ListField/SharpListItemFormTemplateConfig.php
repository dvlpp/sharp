<?php

namespace Dvlpp\Sharp\Config\FormFields\ListField;

use Dvlpp\Sharp\Config\Utils\HasFormTemplateTrait;

class SharpListItemFormTemplateConfig
{
    use HasFormTemplateTrait;

    /**
     * @return static
     */
    public static function create()
    {
        return new static;
    }
}