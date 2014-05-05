<?php namespace Dvlpp\Sharp\Repositories;


interface SharpListEloquentHelperInterface {
    function createNewListItem($instance, $listKey);
    function updateFileAttribute($item, $attr, $value, $listKey=null);
} 