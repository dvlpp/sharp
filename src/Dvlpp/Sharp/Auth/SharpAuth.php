<?php namespace Dvlpp\Sharp\Auth;


interface SharpAuth {

    /**
     * Return true if a CMS admin is logged in.
     *
     * @return boolean
     */
    function checkAdmin();

    /**
     * Logs the user in.
     * Must return the user login.
     *
     * @param $login
     * @param $password
     * @internal param $data
     * @return mixed
     */
    function login($login, $password);

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
     * @param $login
     * @param $type
     * @param $action
     * @param $key
     * @return boolean
     */
    function checkAccess($login, $type, $action, $key);
} 