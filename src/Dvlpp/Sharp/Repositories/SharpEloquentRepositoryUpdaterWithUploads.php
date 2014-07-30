<?php namespace Dvlpp\Sharp\Repositories;


/**
 * Controller which implements this can use SharpEloquentRepositoryUpdaterTrait
 * with uploads in form
 *
 * Interface SharpEloquentRepositoryUpdaterWithUploads
 * @package Dvlpp\Sharp\Repositories
 */
interface SharpEloquentRepositoryUpdaterWithUploads {

    /**
     * Must return the folder where to put the designated upload.
     * Folder will be created if needed.
     *
     * @param $entity
     * @param $attr
     * @return mixed
     */
    function getFileUploadPath($entity, $attr);

    /**
     * Must update the upload in the database, depending on implementation.
     *
     * @param $entity
     * @param $attr
     * @param $file
     * @return mixed
     */
    function updateFileUpload($entity, $attr, $file);

    /**
     * Delete the upload on the database, depending on implementation.
     *
     * @param $entity
     * @param $attr
     * @return mixed
     */
    function deleteFileUpload($entity, $attr);
} 