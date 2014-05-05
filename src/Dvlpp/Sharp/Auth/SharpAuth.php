<?php namespace Dvlpp\Sharp\Auth;


interface SharpAuth {
    function checkAdmin();
    function login($data);
    function logout();
    function checkAccess($user, $type, $action, $key);
} 