<?php namespace Dvlpp\Sharp\Repositories;


interface SharpIsReorderable {

    /**
     * Reorder instances to match the given id array.
     *
     * @param array $entitiesIds
     * @return mixed
     */
    function reorder(Array $entitiesIds);
} 