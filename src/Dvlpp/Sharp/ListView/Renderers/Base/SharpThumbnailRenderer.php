<?php

namespace Dvlpp\Sharp\ListView\Renderers\Base;

use Dvlpp\Sharp\ListView\Renderers\SharpRenderer;
use HTML;

class SharpThumbnailRenderer implements SharpRenderer
{

    function render($instance, $key, $options)
    {
        if ($instance->$key) {
            $w = $h = 100;

            if (str_contains($options, "x")) {
                list($w, $h) = explode("x", $options);
            }

            try {
                return HTML::image(sharp_thumbnail($instance->getSharpFilePathFor($key), $w, $h), "",
                    ["class" => "img-responsive"]);
            } catch(\Exception $e) {
                return "";
            }
        }

        return null;
    }

}