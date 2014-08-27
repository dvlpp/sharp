<?php namespace Dvlpp\Sharp\Commands;

use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use App;
use Dvlpp\Sharp\ListView\SharpEntitiesListParams;

/**
 * Handle command calls.
 *
 * Class CommandsManager
 * @package Dvlpp\Sharp\Commands
 */
class CommandsManager {

    /**
     * Execute the entity command code identified by $entity and $commandKey for instance $instanceId
     *
     * @param $entity
     * @param $commandKey
     * @param $instanceId
     * @return mixed
     */
    public function executeEntityCommand($entity, $commandKey, $instanceId)
    {
        $commandHandler = $this->getCommandHandler($entity->commands->entity->$commandKey, $commandKey, true);

        return $commandHandler->execute($instanceId);
    }

    /**
     * Execute the entities list command code identified by $entity and $commandKey
     *
     * @param $entity
     * @param $commandKey
     * @param SharpEntitiesListParams $entitiesListParams
     * @return mixed
     */
    public function executeEntitiesListCommand($entity, $commandKey, SharpEntitiesListParams $entitiesListParams)
    {
        $commandHandler = $this->getCommandHandler($entity->commands->list->$commandKey, $commandKey);

        return $commandHandler->execute($entitiesListParams);
    }


    /**
     * Find the command handler class.
     *
     * @param $commandConfig
     * @param $commandKey
     * @param bool $isEntity
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException
     */
    private function getCommandHandler($commandConfig, $commandKey, $isEntity=false)
    {
        if(!$commandConfig->data)
        {
            // TODO No configuration for this command
        }

        $commandHandlerClassName = $commandConfig->handler;

        if(!class_exists($commandHandlerClassName))
        {
            // No handler for this command
            throw new MandatoryClassNotFoundException("Command handler [$commandHandlerClassName] for [$commandKey] not found.");
        }

        $commandHandler = App::make($commandHandlerClassName);

        if(($isEntity && !$commandHandler instanceof EntityCommand) || (!$isEntity && !$commandHandler instanceof EntitiesListCommand))
        {
            // Handler isn't implementing correct interface
            throw new MandatoryClassNotFoundException("Command handler [$commandHandlerClassName] for ["
                    ."$commandKey] must implement EntityCommand or EntitiesListCommand.");
        }

        return $commandHandler;
    }

} 