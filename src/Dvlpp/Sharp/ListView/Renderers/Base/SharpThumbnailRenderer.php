<?php namespace Dvlpp\Sharp\ListView\Renderers\Base;

use Dvlpp\Sharp\ListView\Renderers\SharpRenderer;
use HTML;
use Str;

class SharpThumbnailRenderer implements SharpRenderer {

    function render($instance, $key, $options)
    {
        if($instance->$key)
        {
            $w = $h = 100;
            if(Str::contains($options, "x"))
            {
                list($w, $h) = explode("x", $options);
            }
            return HTML::image(sharp_thumbnail($instance->getSharpFilePathFor($key), $w, $h), "", ["class"=>"img-responsive"]);
        }
        return null;
    }

}