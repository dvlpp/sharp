<?php namespace Dvlpp\Sharp\Repositories;

/**
 * Interface SharpHasActivateState
 * @package Dvlpp\Sharp\Repositories
 */
interface SharpHasActivateState {

    /**
     * @param $id
     * @return mixed
     */
    function activate($id);

    /**
     * @param $id
     * @return mixed
     */
    function deactivate($id);
} 