<?php

namespace Dvlpp\Sharp\Repositories;

interface SharpHasCustomSearch
{
    /**
     * Perform the search and return an array of arrays.
     *
     * @param string $field
     * @param string $query
     * @return mixed
     */
    function performCustomSearch($field, $query);

    /**
     * Return an array with the match result for the given id.
     *
     * @param mixed $id
     * @return mixed
     */
    function getCustomSearchResult($id);
}