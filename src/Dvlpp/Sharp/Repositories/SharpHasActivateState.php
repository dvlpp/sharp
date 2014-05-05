<?php namespace Dvlpp\Sharp\Repositories;

/**
 * Interface SharpHasActivateState
 * @package Dvlpp\Sharp\Repositories
 */
interface SharpHasActivateState {
    function activate($id);
    function deactivate($id);
} 