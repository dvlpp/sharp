<?php namespace Dvlpp\Sharp\Repositories;

interface SharpEloquentRepositoryUpdaterWithImageAlteration {

    /**
     * Image uploaded has been altered, and save in filename
     *
     * @param $instance
     * @param $key
     * @param $filename
     * @return mixed
     */
    function imageUploadedUpdated($instance, $key, $filename);
} 