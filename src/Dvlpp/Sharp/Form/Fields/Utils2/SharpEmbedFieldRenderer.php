<?php namespace Dvlpp\Sharp\Form\Fields\Utils;

/**
 * Form Renderer of an Embed type field.
 *
 * Interface SharpEmbedFieldRenderer
 * @package Dvlpp\Sharp\Form\Fields\Utils
 */
interface SharpEmbedFieldRenderer {

    /**
     * Returns the HTML to be displayed for the embed field.
     *
     * @param $instance
     * @param $owner
     * @return string
     */
    function render($instance, $owner);

} 