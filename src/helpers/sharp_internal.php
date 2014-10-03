<?php

if ( ! function_exists('get_entity_update_form_route'))
{
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
        if($instance->{$entity->id_attribute} && !$instance->__sharp_duplication)
        {
            return ["cms.update", $category->key, $entity->key, $instance->{$entity->id_attribute}];
        }
        else
        {
            return ["cms.store", $category->key, $entity->key];
        }
    }
}


if ( ! function_exists('get_embedded_entity_update_form_route'))
{
    /**
     * Generate the embedded entity update form action attribute.
     *
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @param $masterFieldKey
     * @param $category
     * @param $entity
     * @param $instance
     * @return array
     */
    function get_embedded_entity_update_form_route($masterCategoryKey, $masterEntityKey, $masterFieldKey, $category, $entity, $instance)
    {
        if($instance->{$entity->id_attribute} && !$instance->__sharp_duplication)
        {
            return ["cms.embedded.update", $masterCategoryKey, $masterEntityKey, $masterFieldKey, $category->key, $entity->key, $instance->{$entity->id_attribute}];
        }
        else
        {
            return ["cms.embedded.store", $masterCategoryKey, $masterEntityKey, $masterFieldKey, $category->key, $entity->key];
        }
    }
}
