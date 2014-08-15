<?php namespace Dvlpp\Sharp\Repositories;


/**
 * Interface SharpCmsRepository
 * @package Dvlpp\Sharp\Repositories
 */
interface SharpCmsRepository {

    /**
     * Find an instance with the given id.
     *
     * @param $id
     * @return mixed
     */
    function find($id);

    /**
     * List all instances, with optional sorting and search.
     *
     * @param null $sortedColumn
     * @param null $sortedDirection
     * @param null $search
     * @return mixed
     */
    function listAll($sortedColumn=null, $sortedDirection=null, $search=null);

    /**
     * Paginate instances.
     *
     * @param $count
     * @param null $sortedColumn
     * @param null $sortedDirection
     * @param null $search
     * @return mixed
     */
    function paginate($count, $sortedColumn=null, $sortedDirection=null, $search=null);

    /**
     * Create a new instance for initial population of create form.
     *
     * @return mixed
     */
    function newInstance();

    /**
     * Persists an instance.
     *
     * @param array $data
     * @return mixed
     */
    function create(Array $data);

    /**
     * Update an instance.
     *
     * @param $id
     * @param array $data
     * @return mixed
     */
    function update($id, Array $data);

    /**
     * Reorder instances to match the given id array.
     *
     * @param array $entitiesIds
     * @return mixed
     */
    function reorder(Array $entitiesIds);

    /**
     * Delete an instance.
     *
     * @param $id
     * @return mixed
     */
    function delete($id);
} 