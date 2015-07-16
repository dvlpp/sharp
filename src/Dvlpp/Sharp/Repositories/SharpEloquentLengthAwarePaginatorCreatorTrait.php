<?php namespace Dvlpp\Sharp\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

/**
 * Class SharpEloquentLengthAwarePaginatorCreatorTrait
 * @package Dvlpp\Sharp\Repositories
 * @deprecated
 */
trait SharpEloquentLengthAwarePaginatorCreatorTrait {

    /**
     * create aLengthAwarePaginator from an Eloquent Request
     * This is useful because Laravel 5 only provide a simple paginator
     * out of the box.
     *
     * @param $builder
     * @param $perPage
     * @return LengthAwarePaginator
     */
    function createPaginator($builder, $perPage)
    {
        $page = Paginator::resolveCurrentPage();
        $builder->skip(($page - 1) * $perPage)->take($perPage + 1);
        $queryClone = clone ($builder->getQuery());
        $total = $queryClone->skip(0)->take($perPage + 1)->count();

        return new LengthAwarePaginator($builder->get(), $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath()
        ]);
    }
} 