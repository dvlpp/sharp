<?php


use Dvlpp\Sharp\Config\SharpCmsConfig;
use Dvlpp\Sharp\Exceptions\InstanceNotFoundException;
use Dvlpp\Sharp\Exceptions\ValidationException;
use Dvlpp\Sharp\ListView\SharpEntitiesList;

/**
 * Class CmsController
 */
class CmsController extends BaseController {

    /**
     * @return mixed
     */
    public function index()
    {
        return View::make('sharp::cms.index');
    }

    /**
     * @param $categoryName
     * @return mixed
     */
    public function category($categoryName)
    {
        // Find Category config (from sharp CMS config file)
        $category = SharpCmsConfig::findCategory($categoryName);

        $entityName = $category->entities->current();

        return Redirect::route("cms.list", [$categoryName, $entityName]);
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @return mixed
     */
    public function listEntities($categoryName, $entityName)
    {
        if(!sizeof(Input::all()) && Session::get('listViewInput'))
        {
            // We have saved an old "input", which means we need to display the list
            // with some pagination, or sorting, or search config. We simply redirect
            // with the correct querystring based on old input
            $input = Session::get('listViewInput');
            return Redirect::route('cms.list', array_merge(["category"=>$categoryName, "entity"=>$entityName], $input));
        }
        else
        {
            // Save input (we can use it later, see up)
            Session::flash('listViewInput', Input::only('page','sort','dir','search','sub'));
        }

        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = App::make($entity->repository);

        // Grab entities (input is managed there, for search, pagination, ...)
        $entitiesList = new SharpEntitiesList($entity, $repo);

        // And return the View
        return View::make('sharp::cms.entityList', [
            'instances'=>$entitiesList->getInstances(),
            'category'=>SharpCmsConfig::findCategory($categoryName),
            'entity'=>$entity,
            'entityKey'=>$entityName,
            'totalCount'=>$entitiesList->getCount(),
            'pagination'=>$entitiesList->getPagination(),
            'subLists'=>$entitiesList->getSublists(),
            'subList'=>$entitiesList->getSublistId(),
            'sortedColumn'=>$entitiesList->getSortedColumn(),
            'sortedDirection'=>$entitiesList->getSortedDirection()
        ]);
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param $id
     * @return mixed
     */
    public function editEntity($categoryName, $entityName, $id)
    {
        Session::keep('listViewInput');
        return $this->form($categoryName, $entityName, $id);
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @return mixed
     */
    public function createEntity($categoryName, $entityName)
    {
        return $this->form($categoryName, $entityName, null, true);
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param $id
     * @return mixed
     */
    public function updateEntity($categoryName, $entityName, $id)
    {
        Session::keep('listViewInput');
        return $this->save($categoryName, $entityName, $id);
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @return mixed
     */
    public function storeEntity($categoryName, $entityName)
    {
        return $this->save($categoryName, $entityName, null, true);
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param $id
     * @return mixed
     */
    public function ax_activateEntity($categoryName, $entityName, $id)
    {
        return $this->activateDeactivateEntity($categoryName, $entityName, $id, true);
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param $id
     * @return mixed
     */
    public function ax_deactivateEntity($categoryName, $entityName, $id)
    {
        return $this->activateDeactivateEntity($categoryName, $entityName, $id, false);
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @return mixed
     */
    public function ax_reorderEntities($categoryName, $entityName)
    {
        $entities = Input::get("entities");

        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = App::make($entity->repository);

        // Reorder
        $repo->reorder($entities);

        return Response::json(["ok"=>true]);
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param $id
     * @return mixed
     */
    public function destroyEntity($categoryName, $entityName, $id)
    {
        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = App::make($entity->repository);

        $repo->delete($id);

        return Redirect::route("cms.list", [$categoryName, $entityName]);
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param $id
     * @param $activate
     * @return mixed
     */
    private function activateDeactivateEntity($categoryName, $entityName, $id, $activate)
    {
        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = App::make($entity->repository);

        // Activate / deactivate
        $activate ? $repo->activate($id) : $repo->deactivate($id);

        return Response::json(["ok"=>true]);
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param $id
     * @param bool $creation
     * @throws Dvlpp\Sharp\Exceptions\InstanceNotFoundException
     * @return mixed
     */
    private function form($categoryName, $entityName, $id, $creation=false)
    {
        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = App::make($entity->repository);

        // Retrieve the corresponding DB entity
        $instance = $creation ? $repo->newInstance() : $repo->find($id);

        if($instance)
        {
            // And return the View
            return View::make('sharp::cms.entityForm', [
                'instance'=>$instance,
                'entity'=>$entity,
                'entityKey'=>$entityName,
                'category'=>SharpCmsConfig::findCategory($categoryName)
            ]);
        }
        else
        {
            throw new InstanceNotFoundException("Instance of id [$id] and type [$categoryName.$entityName] can't be found");
        }

    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param $id
     * @param bool $creation
     * @return mixed
     */
    private function save($categoryName, $entityName, $id, $creation=false)
    {
        $data = Input::all();

        try {
            // Find Entity config (from sharp CMS config file)
            $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

            // Instantiate the entity repository
            $repo = App::make($entity->repository);

            // First : validation
            if($entity->validator)
            {
                $validator = App::make($entity->validator);
                $validator->validate($data, !$creation?$id:null);
            }

            // Then : update (calling repo)
            if($creation)
            {
                $repo->create($data);
            }
            else
            {
                $repo->update($id, $data);
            }

            // And redirect
            return Redirect::route("cms.list", [$categoryName, $entityName]);
        }

        catch(ValidationException $e)
        {
            return Redirect::back()->withInput()->withErrors($e->getErrors());
        }
    }


} 