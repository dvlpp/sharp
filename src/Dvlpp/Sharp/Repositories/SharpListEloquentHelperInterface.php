<?php namespace Dvlpp\Sharp\Repositories;


interface SharpListEloquentHelperInterface {
    function createNewListItem($instance, $listKey);
    function updateListItemFileAttribute($instance, $key, $file, $listKey);
} 