<?php

namespace Dvlpp\Sharp\Http\Middleware;

trait WithSharpVersion
{
    public function addVersionToView()
    {
        view()->share(
            'sharpVersion',
            file_get_contents(__DIR__ . "/../../../../../version.txt")
        );
    }
}