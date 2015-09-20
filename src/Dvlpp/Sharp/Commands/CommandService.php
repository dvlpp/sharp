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
     * @return SharpCommandReturn
     * @throws MandatoryClassNotFoundException
     */
    public function executeEntityCommand(SharpEntity $entity, $commandKey, $instanceId)
    {
        $commandHandler = $this->getCommandHandler($entity->commands->entity->$commandKey, $commandKey, true);

        return $commandHandler->execute($instanceId);
    }

    /**
     * @param SharpEntity $entity
     * @param $commandKey
     * @return array|null
     */
//    public function getEntityCommandForm(SharpEntity $entity, $commandKey)
//    {
//        if (!$entity->commands->entity->$commandKey->form) {
//            return null;
//        }
//
//        $formFields = [];
//
//        foreach ($entity->commands->entity->$commandKey->form as $fieldKey => $fieldConfig) {
//            $fieldConfigObject = (object)$fieldConfig;
//            $this->addAttributesIfMissing($fieldConfigObject,
//                ["label", "attributes", "conditional_display", "field_width", "help"]);
//
//            $formFields[$fieldKey] = $fieldConfigObject;
//        }
//
//        return $formFields;
//    }

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

        $commandHandler = $this->getCommandHandler($entity->commands->list->$commandKey, $commandKey);

        return $commandHandler->execute($entitiesListParams);
    }


    /**
     * Find the command handler class.
     *
     * @param $commandConfig
     * @param $commandKey
     * @param bool $isForEntity
     * @return SharpCommandReturn
     * @throws MandatoryClassNotFoundException
     */
    private function getCommandHandler($commandConfig, $commandKey, $isForEntity = false)
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

//    private function addAttributesIfMissing(&$fieldConfigObject, $attributes)
//    {
//        foreach ($attributes as $attr) {
//            if (!isset($fieldConfigObject->$attr)) {
//                $fieldConfigObject->$attr = null;
//            }
//        }
//    }

} 