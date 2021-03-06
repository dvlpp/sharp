<?php

namespace Dvlpp\Sharp\Config\Commands;

use Dvlpp\Sharp\Config\Utils\HasFormTemplateTrait;
use Dvlpp\Sharp\Config\Utils\SharpFormTemplate;

class SharpCommandFormTemplateConfig implements SharpFormTemplate
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