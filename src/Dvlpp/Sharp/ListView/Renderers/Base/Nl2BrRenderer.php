<?php namespace Dvlpp\Sharp\ListView\Renderers\Base;


use Dvlpp\Sharp\ListView\Renderers\SharpRenderer;

class Nl2BrRenderer implements SharpRenderer {

    function render($instance, $key, $options)
    {
        if($instance->$key)
        {
            return nl2br($instance->$key);
        }
        return null;
    }

} 