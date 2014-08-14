<?php namespace Dvlpp\Sharp\ListView\Renderers;


use Dvlpp\Sharp\Config\Entities\SharpEntityListTemplateColumn;
use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use App;
use Str;

class SharpColumnRendererManager {

    private static $renderersCache = [];

    private static $baseRenderers = [
        "thumbnail" => '\Dvlpp\Sharp\ListView\Renderers\Base\SharpThumbnailRenderer',
        "charlimit" => '\Dvlpp\Sharp\ListView\Renderers\Base\SharpCharLimitRenderer',
        "markdownWordlimit" => '\Dvlpp\Sharp\ListView\Renderers\Base\Markdown\SharpWordLimitRenderer',
        "date" => '\Dvlpp\Sharp\ListView\Renderers\Base\DateRenderer',
        "nl2br" => '\Dvlpp\Sharp\ListView\Renderers\Base\Nl2BrRenderer',
    ];

    public static function render(SharpEntityListTemplateColumn $col, $colKey, $instance)
    {
        $rendererName = $col->renderer;
        $options = null;
        if(Str::contains($rendererName, ":"))
        {
            $pos = strpos($rendererName, ':');
            $options = substr($rendererName, $pos+1);
            $rendererName = substr($rendererName, 0, $pos);
        }

        $renderer = self::getRenderer($rendererName);

        if(!$renderer instanceof SharpRenderer)
        {
            throw new MandatoryClassNotFoundException("Class [".get_class($renderer)."] must implement Dvlpp\\Sharp\\ListView\\Renderers\\SharpRenderer interface.");
        }

        return $renderer->render($instance, $colKey, $options);
    }

    private static function getRenderer($rendererName)
    {
        // First try the base renderers...
        if(array_key_exists($rendererName, self::$baseRenderers))
        {
            $rendererName = self::$baseRenderers[$rendererName];
        }

        // Look for cache
        if(!array_key_exists($rendererName, self::$renderersCache))
        {
            $renderer = App::make($rendererName);
            self::$renderersCache[$rendererName] = $renderer;
        }

        // Return renderer
        return self::$renderersCache[$rendererName];
    }
}