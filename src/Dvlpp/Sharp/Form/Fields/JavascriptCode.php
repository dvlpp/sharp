<?php

namespace Dvlpp\Sharp\Form\Fields;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

class JavascriptCode
{
    private $field;

    /**
     * JavascriptCode constructor.
     */
    public function __construct($field)
    {
        $this->field = $field;
    }

    public function make()
    {
        $src = public_path($this->field->src);

        if(!file_exists($src) || !is_file($src)) {
            throw new FileNotFoundException("File [$src] not found");
        }

        return '<script>' . file_get_contents($src) . '</script>';
    }
}