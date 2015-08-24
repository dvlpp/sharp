<?php

namespace Dvlpp\Sharp\Commands;

interface SharpEntityCommandWithParams
{

    /**
     * Execute the entity command, and return a file name (if command type is "download"),
     * an array of data for a view (case "view"), or nothing.
     *
     * @param $instanceId
     * @param array $params
     * @return mixed
     */
    function execute($instanceId, array $params);

    /**
     * Validate the posted params.
     *
     * @param array $params
     * @return mixed
     */
    function validate(array $params);

}