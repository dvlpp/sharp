<?php namespace Dvlpp\Sharp\Repositories;

/**
 * Interface SharpHasSublist
 * @package Dvlpp\Sharp\Repositories
 *
 * Project repositories have to implement this interface if sublist config attribute is set.
 * Sublists are useful to group entities.
 */
interface SharpHasSublist {
    function initCurrentSublistId($sublist);
    function getCurrentSublistId();
    function getSublists();
} 