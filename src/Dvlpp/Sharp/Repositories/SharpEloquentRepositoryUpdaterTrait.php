<?php namespace Dvlpp\Sharp\Repositories;

/**
 * Class SharpEloquentRepositoryUpdaterTrait
 * @package Dvlpp\Sharp\Repositories
 */
trait SharpEloquentRepositoryUpdaterTrait {

    /**
     * Updates an entity with the posted data.
     *
     * @param $categoryName
     * @param $entityName
     * @param $instance
     * @param array $data
     * @return mixed
     */
    function updateEntity($categoryName, $entityName, $instance, Array $data)
    {
        $autoUpdaterService = new SharpEloquentAutoUpdaterService;
        return $autoUpdaterService->updateEntity($this, $categoryName, $entityName, $instance, $data);
    }

} 