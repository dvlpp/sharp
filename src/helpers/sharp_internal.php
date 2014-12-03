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

if ( ! function_exists('append_counter_to_filename'))
{
    /**
     * Appends an incremental counter to a file name.
     *
     * @param $file
     * @return string
     */
    function append_counter_to_filename($file)
    {
        if( ! File::exists($file)) return $file;

        $filename = basename($file);
        $ext = File::extension($file);
        if($ext)
        {
            $ext = ".$ext";
            $filename = substr($filename, 0, strlen($filename) - strlen($ext));
        }

        $increment = 1;

        if(preg_match('/(.)+_\d+/', $filename))
        {
            $pos = strrpos($file, "_");
            $filename = substr($filename, 0, $pos);
            $increment = intval(substr($filename, $pos+1)) +1;
        }

        return $filename . "_" . $increment . $ext;
    }
}