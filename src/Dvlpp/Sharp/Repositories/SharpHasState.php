<?php

namespace Dvlpp\Sharp\Repositories;

use Dvlpp\Sharp\Exceptions\InvalidStateException;

/**
 * Interface SharpHasState
 * @package Dvlpp\Sharp\Repositories
 */
interface SharpHasState {

    /**
     * Change the entity state and return the new state.
     * Throw an InvalidStateException in case of error.
     *
     * @param $id
     * @param $state
     * @return string
     * @throws InvalidStateException
     */
    function changeState($id, $state);

}