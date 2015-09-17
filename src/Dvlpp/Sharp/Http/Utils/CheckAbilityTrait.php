<?php

namespace Dvlpp\Sharp\Http\Utils;

trait CheckAbilityTrait
{

    /**
     * Checks an ability, and abort 403 if not.
     *
     * @param $name
     * @param $categoryKey
     * @param null $entityKey
     * @param null $entityId
     * @param array $params
     */
    protected function checkAbility($name, $categoryKey, $entityKey=null, $entityId=null, array $params = [])
    {
        if(!check_ability($name, $categoryKey, $entityKey, $entityId, $params)) {
            abort(403);
        }
    }
}