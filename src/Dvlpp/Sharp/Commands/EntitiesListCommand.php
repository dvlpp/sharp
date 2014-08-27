<?php namespace Dvlpp\Sharp\Commands;

use Dvlpp\Sharp\ListView\SharpEntitiesListParams;

interface EntitiesListCommand {

    /**
     * Execute the entities list command, and return
     * a file name (if command type is "download"),
     * an array of data for a view (case "view"),
     * or nothing.
     *
     * @param \Dvlpp\Sharp\ListView\SharpEntitiesListParams $entitiesListParams
     * @return mixed
     */
    function execute(SharpEntitiesListParams $entitiesListParams);

} 