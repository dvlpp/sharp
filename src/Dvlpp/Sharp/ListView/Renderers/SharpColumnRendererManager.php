<?php

namespace Dvlpp\Sharp\ListView\Renderers;

use Dvlpp\Sharp\Config\SharpListTemplateColumnConfig;
use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;

/**
 * Class SharpColumnRendererManager
 * @package Dvlpp\Sharp\ListView\Renderers
 */
class SharpColumnRendererManager {

    private static $renderersCache = [];

    private static $baseRenderers = [
        "thumbnail" => '\Dvlpp\Sharp\ListView\Renderers\Base\SharpThumbnailRenderer',
        "charlimit" => '\Dvlpp\Sharp\ListView\Renderers\Base\SharpCharLimitRenderer',
        "markdownWordlimit" => '\Dvlpp\Sharp\ListView\Renderers\Base\Markdown\SharpWordLimitRenderer',
        "date" => '\Dvlpp\Sharp\ListView\Renderers\Base\DateRenderer',
        "nl2br" => '\Dvlpp\Sharp\ListView\Renderers\Base\Nl2BrRenderer',
    ];

    /**
     * @param SharpListTemplateColumnConfig $column
     * @param $instance
     * @return string
     * @throws MandatoryClassNotFoundException
     */
    public static function render(SharpListTemplateColumnConfig $column, $instance)
    {
        $rendererName = $column->columnRenderer();
        $options = null;
        if(str_contains($rendererName, ":")) {
            $pos = strpos($rendererName, ':');
            $options = substr($rendererName, $pos+1);
            $rendererName = substr($rendererName, 0, $pos);
        }

        $renderer = self::getRenderer($rendererName);

        if(!$renderer instanceof SharpRenderer) {
            throw new MandatoryClassNotFoundException("Class [".get_class($renderer)."] must implement ["
            . SharpRenderer::class . "] interface.");
        }

        return $renderer->render($instance, $column->key(), $options);
    }

    private static function getRenderer($rendererName)
    {
        // First try the base renderers...
        if(array_key_exists($rendererName, self::$baseRenderers)) {
            $rendererName = self::$baseRenderers[$rendererName];
        }

        // Look for cache
        if(!array_key_exists($rendererName, self::$renderersCache)) {
            $renderer = app($rendererName);
            self::$renderersCache[$rendererName] = $renderer;
        }

        // Return renderer
        return self::$renderersCache[$rendererName];
    }
}