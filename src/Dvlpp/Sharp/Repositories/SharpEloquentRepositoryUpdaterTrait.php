<?php namespace Dvlpp\Sharp\Repositories;

use Dvlpp\Sharp\Repositories\AutoUpdater\SharpEloquentAutoUpdaterService;
use DB;

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
        // Start a transaction
        DB::connection()->getPdo()->beginTransaction();

        $autoUpdaterService = new SharpEloquentAutoUpdaterService;
        $instance = $autoUpdaterService->updateEntity($this, $categoryName, $entityName, $instance, $data);

        DB::connection()->getPdo()->commit();

        return $instance;
    }

} 