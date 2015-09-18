<?php

namespace Dvlpp\Sharp\Http;

use Dvlpp\Sharp\Commands\ReturnTypes\SharpCommandReturn;
use Dvlpp\Sharp\Commands\SharpCommandsManager;
use Dvlpp\Sharp\Config\SharpCmsConfig;
use Dvlpp\Sharp\Exceptions\CommandValidationException;
use Dvlpp\Sharp\Http\Utils\CheckAbilityTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Controller which manages project specific commands on entities list or on entity.
 *
 * Class CmsCommandsController
 */
class CmsCommandsController extends Controller
{
    use CheckAbilityTrait;

    /**
     * @var \Dvlpp\Sharp\Commands\SharpCommandsManager
     */
    private $commandsManager;

    function __construct(SharpCommandsManager $commandsManager)
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
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    public function entitiesListCommand($categoryName, $entityName, $commandKey, Request $request)
    {
        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        $this->checkAbility(
            $entity->commands->list->$commandKey->auth ?: "list",
            $categoryName,
            $entityName
        );

        $commandReturn = $this->commandsManager->executeEntitiesListCommand($entity, $commandKey, $request);

        return $this->handleCommandReturn($commandReturn);
    }


    public function entityCommand($categoryName, $entityName, $commandKey, $instanceId)
    {
        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        $this->checkAbility(
            $entity->commands->entity->$commandKey->auth ?: "update",
            $categoryName,
            $entityName,
            $instanceId
        );

        $commandReturn = $this->commandsManager->executeEntityCommand($entity, $commandKey, $instanceId);

        return $this->handleCommandReturn($commandReturn);

//        $commandForm = $this->commandsManager->getEntityCommandForm($entity, $commandKey);
//        $error = false;
//
//        if ($commandForm) {
//            // There's a form attached to the command:
//
//            if (!$request->has("sharp_form_valued")) {
//                // Return the view of the form
//                // to make the user fill parameters before send the command
//                return view("sharp::cms.partials.list.commandForm", [
//                    'fields' => $commandForm,
//                    'url' => route('cms.entityCommand',
//                        array_merge([$categoryName, $entityName, $commandKey, $instanceId], $request->all()))
//                ]);
//            }
//
//            // Form posted: call the command with the values of the form
//            try {
//                $commandReturn = $this->commandsManager->executeEntityCommand(
//                    $entity, $commandKey, $instanceId, $request->only(array_keys($commandForm))
//                );
//            } catch (CommandValidationException $ex) {
//                $commandReturn = $ex->getMessage();
//                $error = true;
//            }
//
//        } else {
//            $commandReturn = $this->commandsManager->executeEntityCommand($entity, $commandKey, $instanceId);
//        }
//
//        return $this->handleCommandReturn(
//            $entity->commands->entity->$commandKey,
//            $commandReturn,
//            $categoryName,
//            $entityName,
//            $request->except(
//                array_merge(
//                    ["_token", "sharp_form_valued"],
//                    ($commandForm ? array_keys($commandForm) : [])
//                )
//            ),
//            $error
//        );
    }


    private function handleCommandReturn(SharpCommandReturn $commandReturn)
    {
        return response()->json($commandReturn->get());
    }

} 