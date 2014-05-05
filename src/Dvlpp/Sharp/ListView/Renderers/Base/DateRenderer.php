<?php namespace Dvlpp\Sharp\ListView\Renderers\Base;


use Dvlpp\Sharp\ListView\Renderers\SharpRenderer;

class DateRenderer implements SharpRenderer {

    protected static $defaultFormat = "%e %b %y";

    function render($instance, $key, $options)
    {
        if($instance->$key)
        {
            $dt = strtotime($instance->$key);
            $format = $options ?: self::$defaultFormat;
            return strftime($format, $dt);
        }
        return null;
    }

} 