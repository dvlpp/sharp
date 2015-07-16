<?php namespace Dvlpp\Sharp\ListView\Renderers;


interface SharpRenderer {

    /**
     * @param $instance
     * @param $key
     * @param $options
     * @return string
     */
    function render($instance, $key, $options);
} 