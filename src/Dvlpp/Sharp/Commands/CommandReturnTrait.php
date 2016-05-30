<?php

namespace Dvlpp\Sharp\Commands;

use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturnAlert;
use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturnDownload;
use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturnReload;
use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturnUrl;
use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturnView;

/**
 * Trait to handle returns in commands.
 *
 * Class CommandReturnTrait
 * @package Dvlpp\Sharp\Commands
 */
trait CommandReturnTrait
{

    /**
     * Display an alert message.
     *
     * @param $title
     * @param $message
     * @return SharpCommandReturnAlert
     */
    public function alertInfo($title, $message)
    {
        return new SharpCommandReturnAlert($title, $message, "info");
    }

    /**
     * Reload current page.
     *
     * @return SharpCommandReturnReload
     */
    public function reload()
    {
        return new SharpCommandReturnReload();
    }

    /**
     * Download a file.
     *
     * @param $relativeFilePath
     * @return SharpCommandReturnDownload
     */
    public function download($relativeFilePath)
    {
        return new SharpCommandReturnDownload($relativeFilePath);
    }

    /**
     * Return a view.
     *
     * @param $viewName
     * @param array $params
     * @return SharpCommandReturnView
     */
    public function view($viewName, $params = [])
    {
        return new SharpCommandReturnView($viewName, $params);
    }

    /**
     * Display an error message.
     *
     * @param $title
     * @param $message
     * @return SharpCommandReturnAlert
     */
    public function error($title, $message)
    {
        return new SharpCommandReturnAlert($title, $message, "error");
    }

    /**
     * Reload current page.
     *
     * @param string $url
     * @return SharpCommandReturnUrl
     */
    public function url($url)
    {
        return new SharpCommandReturnUrl($url);
    }
}