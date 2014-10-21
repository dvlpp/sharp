<?php

use Dvlpp\Sharp\Commands\SharpCommandsManager;
use Dvlpp\Sharp\Config\SharpCmsConfig;
use Dvlpp\Sharp\ListView\SharpEntitiesList;
use Illuminate\Routing\Controller;

/**
 * Controller which manages project specific commands on entities list or on entity.
 *
 * Class CmsCommandsController
 */
class CmsCommandsController extends Controller {

    /**
     * @var Dvlpp\Sharp\Commands\SharpCommandsManager
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
    public function entitiesListCommand($categoryName, $entityName, $commandKey)
    {
        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = App::make($entity->repository);

        // Grab request params (input is managed there, for search, pagination, ...)
        $entitiesList = new SharpEntitiesList($entity, $repo);
        $entitiesListParams = $entitiesList->createParams();

        $commandReturn = $this->commandsManager->executeEntitiesListCommand($entity, $commandKey, $entitiesListParams);

        return $this->handleCommandReturn($entity->commands->list->$commandKey, $commandReturn, $categoryName, $entityName);
    }


    public function entityCommand($categoryName, $entityName, $commandKey, $instanceId)
    {
        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        $commandReturn = $this->commandsManager->executeEntityCommand($entity, $commandKey, $instanceId);

        return $this->handleCommandReturn($entity->commands->entity->$commandKey, $commandReturn, $categoryName, $entityName);
    }


    private function handleCommandReturn($commandConfig, $commandReturn, $categoryName, $entityName)
    {
        $commandType = $commandConfig->type;

        if($commandType == "download")
        {
            // Return file
            return Response::download($commandReturn);
        }

        elseif($commandType == "view")
        {
            // Return view; data is in $commandReturn
            $commandView = $commandConfig->view;
            return Response::view($commandView, $commandReturn);
        }

        // Just reload
        return Redirect::route("cms.list", array_merge([$categoryName, $entityName], Input::all()));
    }

} 