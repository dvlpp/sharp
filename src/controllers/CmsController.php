<?php

use Dvlpp\Sharp\Config\SharpCmsConfig;
use Dvlpp\Sharp\Config\SharpSiteConfig;
use Dvlpp\Sharp\Exceptions\InstanceNotFoundException;
use Dvlpp\Sharp\Exceptions\ValidationException;
use Dvlpp\Sharp\ListView\SharpEntitiesList;

/**
 * Class CmsController
 */
class CmsController {

    /**
     * @return mixed
     */
    public function index()
    {
        return View::make('sharp::cms.index');
    }

    /**
     * Redirects to list of first entity of the selected category.
     *
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
     * List all entities of a given category/entity with pagination, search, and sorting.
     *
     * @param $categoryName
     * @param $entityName
     * @return mixed
     */
    public function listEntities($categoryName, $entityName)
    {
        if(!sizeof(Input::all()) && Session::get("listViewInput_{$categoryName}_{$entityName}"))
        {
            // We have saved an old "input", which means we need to display the list
            // with some pagination, or sorting, or search config. We simply redirect
            // with the correct querystring based on old input
            $input = Session::get("listViewInput_{$categoryName}_{$entityName}");
            return Redirect::route('cms.list', array_merge(["category"=>$categoryName, "entity"=>$entityName], $input));
        }
        else
        {
            // Save input (we can use it later, see up)
            Session::flash("listViewInput_{$categoryName}_{$entityName}", Input::only('page','sort','dir','search','sub'));
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
            'subList'=>$entitiesList->getCurrentSublistId(),
            'sortedColumn'=>$entitiesList->getSortedColumn(),
            'sortedDirection'=>$entitiesList->getSortedDirection()
        ]);
    }

    /**
     * Show edit form of an entity.
     *
     * @param $categoryName
     * @param $entityName
     * @param $id
     * @return mixed
     */
    public function editEntity($categoryName, $entityName, $id)
    {
        Session::keep("listViewInput_{$categoryName}_{$entityName}");
        return $this->form($categoryName, $entityName, $id);
    }

    /**
     * Show create form of an entity.
     *
     * @param $categoryName
     * @param $entityName
     * @return mixed
     */
    public function createEntity($categoryName, $entityName)
    {
        return $this->form($categoryName, $entityName, null);
    }

    /**
     * Show duplicate form of an entity.
     *
     * @param $categoryName
     * @param $entityName
     * @param $id
     * @param null $lang
     * @throws InstanceNotFoundException
     * @return mixed
     */
    public function duplicateEntity($categoryName, $entityName, $id, $lang=null)
    {
        Session::keep("listViewInput_{$categoryName}_{$entityName}");

        if($lang)
        {
            // We have to first change the language
            // (duplication is useful for i18n copy)
            $this->changeLang($lang);
        }

        return $this->form($categoryName, $entityName, $id, true);
    }

    /**
     * Updates an entity.
     *
     * @param $categoryName
     * @param $entityName
     * @param $id
     * @return mixed
     */
    public function updateEntity($categoryName, $entityName, $id)
    {
        Session::keep("listViewInput_{$categoryName}_{$entityName}");
        return $this->save($categoryName, $entityName, $id);
    }

    /**
     * Create an entity.
     *
     * @param $categoryName
     * @param $entityName
     * @return mixed
     */
    public function storeEntity($categoryName, $entityName)
    {
        return $this->save($categoryName, $entityName, null);
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

        return Redirect::back();

        //return Redirect::route("cms.list", [$categoryName, $entityName]);
    }

    /**
     * Switch current language, and redirects back
     *
     * @param $lang
     * @return mixed
     */
    public function lang($lang)
    {
        $this->changeLang($lang);

        return Redirect::back();
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
     * @param bool $duplication
     * @throws Dvlpp\Sharp\Exceptions\InstanceNotFoundException
     * @return mixed
     */
    private function form($categoryName, $entityName, $id, $duplication=false)
    {
        $creation = ($id === null);

        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = App::make($entity->repository);

        // Retrieve the corresponding DB entity
        $instance = $creation ? $repo->newInstance() : $repo->find($id);

        if($instance)
        {
            if(Session::has('masterInstanceData'))
            {
                // We are back from a embedded entity form.
                // We have to repopulate the master form (this form) as it was before
                $formOldDataStr = unserialize(Session::get('masterInstanceData'));
                Session::flashInput($formOldDataStr);
            }

            // Duplication management: we simply add an attribute here
            $instance->__sharp_duplication = $duplication;

            if($duplication && method_exists($repo, "prepareForDuplication"))
            {
                // We call the repository hook for duplication, in case there's some
                // ajusts to make on the instance
                $instance = $repo->prepareForDuplication($instance);
            }

            // And return the View
            return View::make('sharp::cms.entityForm', [
                'instance'=>$instance,
                'entity'=>$entity,
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
     * @return mixed
     */
    private function save($categoryName, $entityName, $id)
    {
        $creation = ($id === null);

        $data = Input::all();

        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = App::make($entity->repository);

        try {
            // First : validation
            if($entity->validator)
            {
                $validator = App::make($entity->validator);
                $validator->validate($data, $id);
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

    private function changeLang($lang)
    {
        $languages = SharpSiteConfig::getLanguages();
        if($languages)
        {
            if(!$lang || !array_key_exists($lang, $languages))
            {
                $lang = array_values($languages)[0];
            }

            Session::put("sharp_lang", $lang);
        }
    }

} 