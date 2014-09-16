<?php namespace Dvlpp\Sharp\ListView\Renderers\Base;

use Dvlpp\Sharp\ListView\Renderers\SharpRenderer;
use Illuminate\Support\Str;

class SharpCharLimitRenderer implements SharpRenderer {

    function render($instance, $key, $options)
    {
        if($instance->$key)
        {
            $limit = 100;
            if($options && intval($options))
            {
                $limit = intval($options);
            }
            return Str::limit($instance->$key, $limit);
        }
        return null;
    }

}