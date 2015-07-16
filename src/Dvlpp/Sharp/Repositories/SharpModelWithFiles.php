<?php namespace Dvlpp\Sharp\Repositories;

/**
 * Entities that declares files (with Sharp files ui) for their form must be linked to Model which
 * implements this interface.
 * Permits the proper file and thumbnail management on the form page.
 *
 * Interface SharpModelWithFiles
 * @package Dvlpp\Sharp\Repositories
 */
interface SharpModelWithFiles {

    /**
     * Return the full path of a file identified by the $attribute.
     *
     * @param $attribute
     * @return mixed
     */
    function getSharpFilePathFor($attribute);

} 