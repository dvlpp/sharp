<?php

namespace Dvlpp\Sharp\Commands;

use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturnAlert;
use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturnDownload;
use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturnReload;
use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturnView;

trait SharpCommandReturnTrait
{
    public function alertInfo($title, $message)
    {
        return new SharpCommandReturnAlert($title, $message, "info");
    }

    public function reload()
    {
        return new SharpCommandReturnReload();
    }

    public function download($fileName, $filePath)
    {
        return new SharpCommandReturnDownload($fileName, $filePath);
    }

    public function view($viewName, $params = [])
    {
        return new SharpCommandReturnView($viewName, $params);
    }

    public function error($title, $message)
    {
        return new SharpCommandReturnAlert($title, $message, "error");
    }
}