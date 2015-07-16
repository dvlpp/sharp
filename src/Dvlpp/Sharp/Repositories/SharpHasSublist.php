<?php namespace Dvlpp\Sharp\Repositories;

/**
 * Project repositories have to implement this interface if sublist config attribute is set.
 * Sublists are useful to group entities.
 *
 * Interface SharpHasSublist
 * @package Dvlpp\Sharp\Repositories
 */
interface SharpHasSublist {
    /**
     * @param $sublist
     * @return mixed
     */
    function initCurrentSublistId($sublist);

    /**
     * @return mixed
     */
    function getCurrentSublistId();

    /**
     * @return mixed
     */
    function getSublists();
} 