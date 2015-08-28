<?php

namespace Dvlpp\Sharp\Repositories;

/**
 * Project repositories have to implement this interface if list_filters
 * config attribute is set. List filters are useful to group entities in lists.
 *
 * Interface SharpHasListFilters
 * @package Dvlpp\Sharp\Repositories
 */
interface SharpHasListFilters
{

    /**
     * Set the current instance id of the $listFilterKey filter
     *
     * @param $listFilterKey
     * @param $listFilterInstanceId
     */
    function initListFilterIdFor($listFilterKey, $listFilterInstanceId);

    /**
     * Return a key-valued array like
     * [
     *      $listFilterKey => $listFilterInstanceId,
     *      ...
     * ]
     *
     * @return array
     */
    function getListFilterCurrents();

    /**
     * Return a key-valued array like
     * [
     *      $listFilterKey => [
     *          $instanceId => $instanceValue,
     *          ...
     *      ],
     *      ...
     * ]
     * @return array
     */
    function getListFilterContents();
} 