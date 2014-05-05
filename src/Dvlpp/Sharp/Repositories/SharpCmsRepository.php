<?php namespace Dvlpp\Sharp\Repositories;


interface SharpCmsRepository {
    function find($id);
    function listAll($sortedColumn=null, $sortedDirection=null);
    function paginate($count, $sortedColumn=null, $sortedDirection=null);

    function newInstance();
    function create(Array $data);
    function update($id, Array $data);
    function reorder(Array $entitiesIds);
    function delete($id);
} 