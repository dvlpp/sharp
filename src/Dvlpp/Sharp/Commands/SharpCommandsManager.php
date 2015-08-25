<?php namespace Dvlpp\Sharp\Commands;

use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\ListView\SharpEntitiesListParams;

/**
 * Handle command calls.
 *
 * Class SharpCommandsManager
 * @package Dvlpp\Sharp\Commands
 */
class SharpCommandsManager {

    /**
     * Execute the entity command code identified by $entity and $commandKey for instance $instanceId
     *
     * @param $entity
     * @param $commandKey
     * @param $instanceId
     * @param array|null $params
     * @return mixed
     * @throws MandatoryClassNotFoundException
     */
    public function executeEntityCommand($entity, $commandKey, $instanceId, $params=null)
    {
        $commandHandler = $this->getCommandHandler($entity->commands->entity->$commandKey, $commandKey, $params, true);

        if($params) $commandHandler->validate($params);

        return $commandHandler->execute($instanceId, $params);
    }

    /**
     * @param $entity
     * @param $commandKey
     * @return array|null
     */
    public function getEntityCommandForm($entity, $commandKey)
    {
        if(!$entity->commands->entity->$commandKey->form) return null;

        $formFields = [];

        foreach($entity->commands->entity->$commandKey->form as $fieldKey => $fieldConfig)
        {
            $fieldConfigObject = (object)$fieldConfig;
            $this->addAttributesIfMissing($fieldConfigObject, ["label", "attributes", "conditional_display", "field_width", "help"]);

            $formFields[$fieldKey] = $fieldConfigObject;
        }

        return $formFields;
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
        $commandHandler = $this->getCommandHandler($entity->commands->list->$commandKey, null, $commandKey);

        return $commandHandler->execute($entitiesListParams);
    }


    /**
     * Find the command handler class.
     *
     * @param $commandConfig
     * @param $commandKey
     * @param array $params
     * @param bool $isEntity
     * @return mixed
     * @throws MandatoryClassNotFoundException
     */
    private function getCommandHandler($commandConfig, $commandKey, $params=null, $isEntity=false)
    {
        if(!$commandConfig->data) throw new MandatoryClassNotFoundException("Command config for [$commandKey] not found.");

        $commandHandlerClassName = $commandConfig->handler;

        if(!class_exists($commandHandlerClassName))
        {
            // No handler for this command
            throw new MandatoryClassNotFoundException("Command handler [$commandHandlerClassName] for [$commandKey] not found.");
        }

        $commandHandler = app($commandHandlerClassName);

        if($isEntity && $params && !$commandHandler instanceof SharpEntityCommandWithParams)
        {
            throw new MandatoryClassNotFoundException("Command handler [$commandHandlerClassName] for ["
                ."$commandKey] must implement EntityCommandWithParams.");
        }
        elseif($isEntity && !$params && !$commandHandler instanceof SharpEntityCommand)
        {
            throw new MandatoryClassNotFoundException("Command handler [$commandHandlerClassName] for ["
                ."$commandKey] must implement EntityCommand.");
        }
        elseif(!$isEntity && !$commandHandler instanceof SharpEntitiesListCommand)
        {
            throw new MandatoryClassNotFoundException("Command handler [$commandHandlerClassName] for ["
                ."$commandKey] must implement EntitiesListCommand.");
        }

        return $commandHandler;
    }

    private function addAttributesIfMissing(&$fieldConfigObject, $attributes)
    {
        foreach($attributes as $attr)
        {
            if (!isset($fieldConfigObject->$attr)) $fieldConfigObject->$attr = null;
        }
    }

} 