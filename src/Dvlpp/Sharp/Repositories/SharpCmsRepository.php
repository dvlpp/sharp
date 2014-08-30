<?php namespace Dvlpp\Sharp\Repositories;
use Dvlpp\Sharp\ListView\SharpEntitiesListParams;


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
     * @param \Dvlpp\Sharp\ListView\SharpEntitiesListParams $params
     * @return mixed
     */
    function listAll(SharpEntitiesListParams $params);

    /**
     * Paginate instances.
     *
     * @param $count
     * @param \Dvlpp\Sharp\ListView\SharpEntitiesListParams $params
     * @return mixed
     */
    function paginate($count, SharpEntitiesListParams $params);

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
     * Delete an instance.
     *
     * @param $id
     * @return mixed
     */
    function delete($id);
} 