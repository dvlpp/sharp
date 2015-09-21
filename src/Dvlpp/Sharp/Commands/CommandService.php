<?php

namespace Dvlpp\Sharp\Commands;

use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturn;
use Dvlpp\Sharp\Config\Entities\SharpEntity;
use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\ListView\SharpEntitiesList;
use Illuminate\Http\Request;

/**
 * Handle command calls.
 *
 * Class SharpCommandsManager
 * @package Dvlpp\Sharp\Commands
 */
class CommandService
{

    /**
     * Execute the entity command code identified by $entity and $commandKey
     * for instance $instanceId
     *
     * @param SharpEntity $entity
     * @param $commandKey
     * @param $instanceId
     * @param Request $request
     * @return SharpCommandReturn
     * @throws MandatoryClassNotFoundException
     */
    public function executeEntityCommand(SharpEntity $entity, $commandKey, $instanceId, Request $request)
    {
        $commandHandler = $this->commandHandler($entity->commands->entity->$commandKey, $commandKey, true);

        return $commandHandler->execute($instanceId, $this->commandFormParams($entity->commands->entity->$commandKey, $request));
    }

    /**
     * Execute the entities list command code identified by $entity and $commandKey
     *
     * @param SharpEntity $entity
     * @param $commandKey
     * @param Request $request
     * @return SharpCommandReturn
     * @throws MandatoryClassNotFoundException
     */
    public function executeEntitiesListCommand(SharpEntity $entity, $commandKey, Request $request)
    {
        // Instantiate the entity repository
        $repo = app($entity->repository);

        // Grab request params (input is managed there, for search, pagination, ...)
        $entitiesListParams = (new SharpEntitiesList($entity, $repo, $request))->createParams();

        $commandHandler = $this->commandHandler($entity->commands->list->$commandKey, $commandKey);

        return $commandHandler->execute($entitiesListParams, $this->commandFormParams($entity->commands->list->$commandKey, $request));
    }

    /**
     * Find the command handler class.
     *
     * @param $commandConfig
     * @param $commandKey
     * @param bool $isForEntity
     * @return SharpEntityCommand|SharpEntitiesListCommand
     * @throws MandatoryClassNotFoundException
     */
    private function commandHandler($commandConfig, $commandKey, $isForEntity = false)
    {
        if (!$commandConfig->data) {
            throw new MandatoryClassNotFoundException("Command config for [$commandKey] not found.");
        }

        $commandHandlerClassName = $commandConfig->handler;

        if (!class_exists($commandHandlerClassName)) {
            // No handler for this command
            throw new MandatoryClassNotFoundException("Command handler [$commandHandlerClassName] for [$commandKey] not found.");
        }

        $commandHandler = app($commandHandlerClassName);

        if ($isForEntity && !$commandHandler instanceof SharpEntityCommand) {
            throw new MandatoryClassNotFoundException("Command handler [$commandHandlerClassName] for ["
                . "$commandKey] must implement EntityCommand.");

        } elseif (!$isForEntity && !$commandHandler instanceof SharpEntitiesListCommand) {
            throw new MandatoryClassNotFoundException("Command handler [$commandHandlerClassName] for ["
                . "$commandKey] must implement EntitiesListCommand.");
        }

        return $commandHandler;
    }

    /**
     * Return posted params for this command.
     *
     * @param $commandConfig
     * @param Request $request
     * @return array
     */
    private function commandFormParams($commandConfig, Request $request)
    {
        $params = [];

        if(!$commandConfig->form) {
            return $params;
        }

        foreach($commandConfig->form as $paramName => $field) {
            if($request->has($paramName)) {
                $params[$paramName] = $request->get($paramName);
            }
        }

        return $params;
    }

}