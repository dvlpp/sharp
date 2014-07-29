<?php namespace Dvlpp\Sharp\Auth;


interface SharpAuth {

    /**
     * Return true if a CMS admin is logged in.
     *
     * @return mixed
     */
    function checkAdmin();

    /**
     * Logs the user in.
     * Must return the user login.
     *
     * @param $data
     * @return mixed
     */
    function login($data);

    /**
     * Logout the user.
     *
     * @return mixed
     */
    function logout();

    /**
     * Check if the user has the permission for the action described
     * by $type (entity), $action (view, update, ...) and $key (entity name).
     *
     * @param $user
     * @param $type
     * @param $action
     * @param $key
     * @return mixed
     */
    function checkAccess($user, $type, $action, $key);
} 