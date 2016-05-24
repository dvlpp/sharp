<?php

use Dvlpp\Sharp\Config\SharpEntityConfig;

/**
 * @return \Illuminate\Contracts\Auth\Guard
 */
function sharp_auth_guard()
{
    if(config("auth.guards.sharp")) {
        return Auth::guard('sharp');
    }

    return Auth::guard();
}

/**
 * Returns true if the authenticated user is a Sharp user.
 *
 * @return bool
 */
function is_sharp_user()
{
    $user = sharp_auth_guard()->user();

    if (!$user) {
        return false;
    }

    return !method_exists($user, 'isSharpUser') || $user->isSharpUser();
}

/**
 * Returns the name of the login field of the user.
 *
 * @return string
 */
function get_user_login_field_name()
{
    $fieldName = config('sharp.user_login_field_name');

    return $fieldName ?: "login";
}

/**
 * Returns the authenticated user login
 *
 * @return string
 */
function get_user_login()
{
    if (!is_sharp_user()) {
        return null;
    }

    return sharp_auth_guard()->user()->{get_user_login_field_name()};
}

/**
 * Check ability with Laravel 5.1 ACL.
 *
 * @param $name
 * @param $categoryKey
 * @param null $entityKey
 * @param null $entityId
 * @param array $params
 * @return bool
 */
function check_ability($name, $categoryKey, $entityKey=null, $entityId=null, array $params = [])
{
    $ability = "sharp::$name.{$categoryKey}";

    if($entityKey) {
        $ability .= ".{$entityKey}";

        if(!check_ability("category", $categoryKey)) {
            // The whole category was forbidden...
            return false;
        }

        if($name != "entity" && !check_ability("entity", $categoryKey, $entityKey)) {
            // The whole entity was forbidden...
            return false;
        }
    }

    // If ability isn't defined, then it's all good
    if(!Gate::has($ability)) return true;

    if($entityId) {
        $params = ["id" => $entityId] + $params;
    }

    return Gate::check($ability, $params);
}

function get_abilited_entities_list_commands(SharpEntityConfig $entity)
{
    $tabCommands = [];

    foreach($entity->listCommandsConfig() as $command) {
        if (check_ability($command->authLevel() ?: "list",
            $entity->categoryKey(), $entity->key())) {
            $tabCommands[$command->key()] = $command;
        }
    }

    return $tabCommands;
}

function get_abilited_entities_entity_commands(SharpEntityConfig $entity, $instanceId)
{
    $tabCommands = [];

    foreach($entity->entityCommandsConfig() as $command) {
        if (check_ability($command->authLevel() ?: "update",
            $entity->categoryKey(), $entity->key(), $instanceId)) {
            $tabCommands[$command->key()] = $command;
        }
    }

    return $tabCommands;
}