<?php

namespace Dvlpp\Sharp\Http;

use Dvlpp\Sharp\Commands\CommandService;
use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturn;
use Dvlpp\Sharp\Exceptions\ValidationException;
use Dvlpp\Sharp\Http\Utils\CheckAbilityTrait;
use Illuminate\Http\Request;

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
        parent::__construct();

        $this->commandsManager = $commandsManager;
    }


    /**
     * Execute a functional code action and return either a view, a file or nothing.
     *
     * @param $categoryKey
     * @param $entityKey
     * @param $commandKey
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    public function entitiesListCommand($categoryKey, $entityKey, $commandKey, Request $request)
    {
        // Find Entity config (from sharp CMS config file)
        $entity = sharp_entity($categoryKey, $entityKey);

        $command = $entity->findListCommand($commandKey);

        $this->checkAbility(
            $command->authLevel() ?: "list",
            $categoryKey,
            $entityKey
        );

        $commandReturn = $this->commandsManager->executeEntitiesListCommand($entity, $command, $request);

        return $this->handleCommandReturn($commandReturn);
    }


    /**
     * @param $categoryKey
     * @param $entityKey
     * @param $commandKey
     * @param $instanceId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    public function entityCommand($categoryKey, $entityKey, $commandKey, $instanceId, Request $request)
    {
        // Find Entity config (from sharp CMS config file)
        $entity = sharp_entity($categoryKey, $entityKey);

        $command = $entity->findEntityCommand($commandKey);

        $this->checkAbility(
            $command->authLevel() ?: "update",
            $categoryKey,
            $entityKey,
            $instanceId
        );

        try {
            $commandReturn = $this->commandsManager->executeEntityCommand($entity, $command, $instanceId, $request);
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