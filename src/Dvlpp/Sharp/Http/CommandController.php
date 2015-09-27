<?php

namespace Dvlpp\Sharp\Http;

use Illuminate\Http\Request;
use Dvlpp\Sharp\Config\SharpConfig;
use Dvlpp\Sharp\Commands\CommandService;
use Dvlpp\Sharp\Http\Utils\CheckAbilityTrait;
use Dvlpp\Sharp\Exceptions\ValidationException;
use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturn;

/**
 * Controller which manages project specific commands on entities list or on entity.
 *
 * Class CmsCommandsController
 */
class CommandController extends Controller
{
    use CheckAbilityTrait;

    /**
     * @var \Dvlpp\Sharp\Commands\CommandService
     */
    private $commandsManager;

    function __construct(CommandService $commandsManager)
    {
        $this->commandsManager = $commandsManager;
    }


    /**
     * Execute a functional code action and return either a view, a file or nothing.
     *
     * @param $categoryName
     * @param $entityName
     * @param $commandKey
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    public function entitiesListCommand($categoryName, $entityName, $commandKey, Request $request)
    {
        // Find Entity config (from sharp CMS config file)
        $entity = SharpConfig::findEntity($categoryName, $entityName);

        $this->checkAbility(
            $entity->commands->list->$commandKey->auth ?: "list",
            $categoryName,
            $entityName
        );

        $commandReturn = $this->commandsManager->executeEntitiesListCommand($entity, $commandKey, $request);

        return $this->handleCommandReturn($commandReturn);
    }


    /**
     * @param $categoryName
     * @param $entityName
     * @param $commandKey
     * @param $instanceId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    public function entityCommand($categoryName, $entityName, $commandKey, $instanceId, Request $request)
    {
        // Find Entity config (from sharp CMS config file)
        $entity = SharpConfig::findEntity($categoryName, $entityName);

        $this->checkAbility(
            $entity->commands->entity->$commandKey->auth ?: "update",
            $categoryName,
            $entityName,
            $instanceId
        );

        try {
            $commandReturn = $this->commandsManager->executeEntityCommand($entity, $commandKey, $instanceId, $request);
            return $this->handleCommandReturn($commandReturn);

        } catch(ValidationException $ex) {
            return $this->handleCommandValidationError($ex);
        }

    }


    private function handleCommandReturn(SharpCommandReturn $commandReturn)
    {
        return response()->json($commandReturn->get());
    }

    private function handleCommandValidationError(ValidationException $ex)
    {
        return response()->json($ex->getErrors(), 422);
    }

} 