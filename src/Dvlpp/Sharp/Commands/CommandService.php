<?php

namespace Dvlpp\Sharp\Commands;

use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturn;
use Dvlpp\Sharp\Config\Commands\SharpCommandConfig;
use Dvlpp\Sharp\Config\Entities\SharpEntity;
use Dvlpp\Sharp\Config\SharpEntityConfig;
use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Exceptions\ValidationException;
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
     * @param SharpEntityConfig $entity
     * @param SharpCommandConfig $command
     * @param $instanceId
     * @param Request $request
     * @return SharpCommandReturn
     * @throws MandatoryClassNotFoundException
     * @throws ValidationException
     */
    public function executeEntityCommand(SharpEntityConfig $entity, SharpCommandConfig $command, $instanceId, Request $request)
    {
        $commandHandler = $this->commandHandler($command, true);

        $commandHandler->validate($request);

        return $commandHandler->execute(
            $instanceId,
            $this->commandFormParams($command, $request));
    }

    /**
     * Execute the entities list command code identified by $entity and $commandKey
     *
     * @param SharpEntityConfig $entity
     * @param SharpCommandConfig $command
     * @param Request $request
     * @return SharpCommandReturn
     * @throws MandatoryClassNotFoundException
     * @throws ValidationException
     */
    public function executeEntitiesListCommand(SharpEntityConfig $entity, SharpCommandConfig $command, Request $request)
    {
        // Instantiate the entity repository
        $repo = app($entity->repository());

        // Grab request params (input is managed there, for search, pagination, ...)
        $entitiesListParams = (new SharpEntitiesList($entity, $repo, $request))->createParams();

        $commandHandler = $this->commandHandler($command);

        $commandHandler->validate($request);

        return $commandHandler->execute(
            $entitiesListParams,
            $this->commandFormParams($command, $request));
    }

    /**
     * Find the command handler class.
     *
     * @param SharpCommandConfig $command
     * @param bool $isForEntity
     * @return SharpEntityCommand|SharpEntitiesListCommand
     * @throws MandatoryClassNotFoundException
     */
    private function commandHandler(SharpCommandConfig $command, $isForEntity = false)
    {
        $commandHandlerClassName = $command->handler();

        if (!class_exists($commandHandlerClassName)) {
            // No handler for this command
            throw new MandatoryClassNotFoundException("Command handler [$commandHandlerClassName] for [{$command->key()}] not found.");
        }

        $commandHandler = app($commandHandlerClassName);

        if ($isForEntity && !$commandHandler instanceof SharpEntityCommand) {
            throw new MandatoryClassNotFoundException("Command handler [$commandHandlerClassName] for ["
                . "{$command->key()}] must implement EntityCommand.");

        } elseif (!$isForEntity && !$commandHandler instanceof SharpEntitiesListCommand) {
            throw new MandatoryClassNotFoundException("Command handler [$commandHandlerClassName] for ["
                . "{$command->key()}] must implement EntitiesListCommand.");
        }

        return $commandHandler;
    }

    /**
     * Return posted params for this command.
     *
     * @param SharpCommandConfig $command
     * @param Request $request
     * @return array
     */
    private function commandFormParams(SharpCommandConfig $command, Request $request)
    {
        $params = [];

        if(!$command->hasForm()) {
            return $params;
        }

        foreach($command->formFieldsConfig() as $field) {
            if($request->has($field->key())) {
                $params[$field->key()] = $request->get($field->key());
            }
        }

        return $params;
    }

}