<?php

namespace Dvlpp\Sharp\Repositories;

/**
 * Controller which implements this can use SharpEloquentRepositoryUpdaterTrait
 * with uploads in form
 *
 * Interface SharpEloquentRepositoryUpdaterWithUploads
 * @package Dvlpp\Sharp\Repositories
 */
interface SharpEloquentRepositoryUpdaterWithUploads {

    /**
     * Must update the upload in the database, depending on implementation.
     *
     * @param $instance
     * @param $attr
     * @param array $fileInfo
     * @return mixed
     */
    function updateFileUpload($instance, $attr, $fileInfo);

    /**
     * Delete the upload on the database, depending on implementation.
     *
     * @param $instance
     * @param $attr
     * @return mixed
     */
    function deleteFileUpload($instance, $attr);

    /**
     * Return the relative storage dir path for this instance.
     *
     * @param $instance
     * @return string
     */
    function getStorageDirPath($instance);
} 