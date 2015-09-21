<?php

namespace Dvlpp\Sharp\Commands;

use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturn;
use Dvlpp\Sharp\ListView\SharpEntitiesListParams;

/**
 * Class SharpEntitiesListCommand
 * @package Dvlpp\Sharp\Commands
 */
abstract class SharpEntitiesListCommand {

    use CommandReturnTrait;

    use CommandValidateTrait;

    /**
     * Execute the command.
     *
     * @param \Dvlpp\Sharp\ListView\SharpEntitiesListParams $entitiesListParams
     * @param array $params
     * @return SharpCommandReturn
     */
    abstract function execute(SharpEntitiesListParams $entitiesListParams, array $params=[]);

} 