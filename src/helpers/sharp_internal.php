<?php

/**
 * Generate the entity update form action attribute.
 *
 * @param $category
 * @param $entity
 * @param $instance
 * @return array
 */
function get_entity_update_form_route($category, $entity, $instance)
{
    if ($instance->{$entity->id_attribute} && !$instance->__sharp_duplication) {
        return ["cms.update", $category->key, $entity->key, $instance->{$entity->id_attribute}];
    } else {
        return ["cms.store", $category->key, $entity->key];
    }
}

/**
 * Appends an incremental counter to a file name.
 *
 * @param $file
 * @return string
 */
function append_counter_to_filename($file)
{
    if (!File::exists($file)) {
        return $file;
    }

    $filename = basename($file);
    $ext = File::extension($file);
    if ($ext) {
        $ext = ".$ext";
        $filename = substr($filename, 0, strlen($filename) - strlen($ext));
    }

    $increment = 1;

    if (preg_match('/(.)+_\d+/', $filename)) {
        $pos = strrpos($file, "_");
        $filename = substr($filename, 0, $pos);
        $increment = intval(substr($filename, $pos + 1)) + 1;
    }

    return $filename . "_" . $increment . $ext;
}

/**
 * Returns the value of a given attribute for an entity.
 *
 * @param $instance
 * @param $attributeName
 * @return string
 */
function get_entity_attribute_value($instance, $attributeName)
{
    if (strpos($attributeName, "~")) {
        // If there's a "~" in the field $key, this means we are in a single relation case
        // (One-To-One or Belongs To). The ~ separate the relation name and the value.
        // For instance : boss~name indicate that the instance as a single "boss" relation,
        // which has a "name" attribute.
        list($relation, $attributeName) = explode("~", $attributeName);

        return $instance->{$relation}->{$attributeName};
    } else {
        return $instance->{$attributeName};
    }
}

/**
 * Returns true if the authenticated user is a Sharp user.
 *
 * @return bool
 */
function is_sharp_user()
{
    $user = auth()->user();
    if (!$user) {
        return false;
    }

    return !method_exists($user, 'isSharpUser') || auth()->user()->isSharpUser();
}

/**
 * Returns the name of the login field of the user.
 *
 * @return string
 */
function get_user_login_field_name()
{
    $class = config('auth.model');

    return method_exists($class, "sharpLoginField")
        ? $class::sharpLoginField()
        : "login";
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

    return auth()->user()->{get_user_login_field_name()};
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
    }

    // If ability isn't defined, then it's all good
    if(!Gate::has($ability)) return true;

    if($entityId) {
        $params = ["id" => $entityId] + $params;
    }

    return Gate::check($ability, $params);
}

function get_abilited_entities_list_commands($category, $entity)
{
    $tabCommands = [];

    if(sizeof($entity->commands->data) && sizeof($entity->commands->list->data)) {

        foreach($entity->commands->list as $command) {
            if (check_ability($entity->commands->list->$command->auth ?: "list",
                $category->key,
                $entity->key)) {
                $tabCommands[$command] = $entity->commands->list->$command;
            }
        }
    }

    return $tabCommands;
}

function get_abilited_entities_entity_commands($category, $entity, $instanceId)
{
    $tabCommands = [];

    if(sizeof($entity->commands->data) && sizeof($entity->commands->entity->data)) {

        foreach($entity->commands->entity as $command) {
            if (check_ability($entity->commands->entity->$command->auth ?: "update",
                $category->key,
                $entity->key,
                $instanceId)) {
                $tabCommands[$command] = $entity->commands->entity->$command;
            }
        }
    }

    return $tabCommands;
}