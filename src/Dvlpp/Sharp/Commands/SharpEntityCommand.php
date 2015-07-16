<?php namespace Dvlpp\Sharp\Commands;

/**
 * Interface SharpEntityCommand
 * @package Dvlpp\Sharp\Commands
 */
interface SharpEntityCommand {

    /**
     * Execute the entity command, and return
     * a file name (if command type is "download"),
     * an array of data for a view (case "view"),
     * or nothing.
     *
     * @param $instanceId
     * @return mixed
     */
    function execute($instanceId);

} 