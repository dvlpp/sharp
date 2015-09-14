<?php namespace Dvlpp\Sharp\Http;

use Dvlpp\Sharp\Auth\SharpAccessManager;
use Dvlpp\Sharp\Commands\SharpCommandsManager;
use Dvlpp\Sharp\Config\SharpCmsConfig;
use Dvlpp\Sharp\Exceptions\CommandValidationException;
use Dvlpp\Sharp\ListView\SharpEntitiesList;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Controller which manages project specific commands on entities list or on entity.
 *
 * Class CmsCommandsController
 */
class CmsCommandsController extends Controller
{

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
     * @return mixed
     */
    public function entitiesListCommand($categoryName, $entityName, $commandKey, Request $request)
    {
        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Have to manage access auth here, because it can be managed from the config
        $granted = SharpAccessManager::granted(
            'entity',
            $entity->commands->list->$commandKey->auth ?: "update",
            $entity->key);

        if (!$granted) {
            return redirect("/");
        }

        // Instantiate the entity repository
        $repo = app($entity->repository);

        // Grab request params (input is managed there, for search, pagination, ...)
        $entitiesListParams = (new SharpEntitiesList($entity, $repo, $request))->createParams();

        $commandReturn = $this->commandsManager->executeEntitiesListCommand($entity, $commandKey, $entitiesListParams);

        return $this->handleCommandReturn(
            $entity->commands->list->$commandKey,
            $commandReturn,
            $categoryName,
            $entityName,
            $request
        );
    }


    public function entityCommand($categoryName, $entityName, $commandKey, $instanceId, Request $request)
    {
        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Have to manage access auth here, because it can be managed from the config
        $granted = SharpAccessManager::granted(
            'entity',
            $entity->commands->entity->$commandKey->auth ?: "update",
            $entity->key);

        if (!$granted) {
            return redirect("/");
        }

        $commandForm = $this->commandsManager->getEntityCommandForm($entity, $commandKey);
        $error = false;

        if ($commandForm) {
            // There's a form attached to the command:

            if (!$request->has("sharp_form_valued")) {
                // Return the view of the form
                // to make the user fill parameters before send the command
                return view("sharp::cms.partials.list.commandForm", [
                    'fields' => $commandForm,
                    'url' => route('cms.entityCommand',
                        array_merge([$categoryName, $entityName, $commandKey, $instanceId], $request->all()))
                ]);
            }

            // Form posted: call the command with the values of the form
            try {
                $commandReturn = $this->commandsManager->executeEntityCommand(
                    $entity, $commandKey, $instanceId, $request->only(array_keys($commandForm))
                );
            } catch (CommandValidationException $ex) {
                $commandReturn = $ex->getMessage();
                $error = true;
            }

        } else {
            $commandReturn = $this->commandsManager->executeEntityCommand($entity, $commandKey, $instanceId);
        }

        return $this->handleCommandReturn(
            $entity->commands->entity->$commandKey,
            $commandReturn,
            $categoryName,
            $entityName,
            $request->except(
                array_merge(
                    ["_token", "sharp_form_valued"],
                    ($commandForm ? array_keys($commandForm) : [])
                )
            ),
            $error
        );
    }


    private function handleCommandReturn($commandConfig, $commandReturn, $categoryName, $entityName, $request = [], $error = false)
    {
        if ($error) {
            return redirect()
                ->route("cms.list", array_merge([$categoryName, $entityName], $request))
                ->with("errorMessage", $commandReturn);
        }

        $commandType = $commandConfig->type;

        if ($commandType == "download") {
            // Return file
            return response()->download($commandReturn);

        } elseif ($commandType == "view") {
            // Return view; data is in $commandReturn
            $commandView = $commandConfig->view;

            return view($commandView, $commandReturn);
        }

        // Just reload
        return redirect()->route("cms.list", array_merge([$categoryName, $entityName], $request));
    }

} 