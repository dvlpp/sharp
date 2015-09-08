<?php

namespace Dvlpp\Sharp\Http;

use Dvlpp\Sharp\Config\SharpCmsConfig;
use Dvlpp\Sharp\Config\SharpSiteConfig;
use Dvlpp\Sharp\Exceptions\InstanceNotFoundException;
use Dvlpp\Sharp\Exceptions\ValidationException;
use Dvlpp\Sharp\Form\Fields\CustomSearchField;
use Dvlpp\Sharp\ListView\SharpEntitiesList;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class CmsController
 */
class CmsController extends Controller
{

    /**
     * @return mixed
     */
    public function index()
    {
        return view('sharp::cms.index');
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

        return redirect()->route("cms.list", [$categoryName, $entityName]);
    }

    /**
     * List all entities of a given category/entity with pagination, search, and sorting.
     *
     * @param $categoryName
     * @param $entityName
     * @param Request $request
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    public function listEntities($categoryName, $entityName, Request $request)
    {
        if ($qs = $this->restoreQuerystringForListEntities($categoryName, $entityName, $request)) {
            // We saved an old "input", which means we need to display the list
            // with some pagination, or sorting, or search config. We simply redirect
            // with the correct querystring based on old input
            return redirect()->route('cms.list',
                array_merge(["category" => $categoryName, "entity" => $entityName], $qs));

        } else {
            // Save input (we can use it later, see up)
            $this->saveQuerystringForListEntities($categoryName, $entityName, $request);
        }

        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = app($entity->repository);

        // Grab entities (input is managed there, for search, pagination, ...)
        $entitiesList = new SharpEntitiesList($entity, $repo, $request);

        // And return the View
        return view('sharp::cms.entityList', [
            'instances' => $entitiesList->getInstances(),
            'category' => SharpCmsConfig::findCategory($categoryName),
            'entity' => $entity,
            'entityKey' => $entityName,
            'totalCount' => $entitiesList->getCount(),
            'pagination' => $entitiesList->getPagination(),
            'subLists' => $entitiesList->getSublists(),
            'subList' => $entitiesList->getCurrentSublistId(),
            'listFilters' => [
                "contents" => $entitiesList->getListFilterContents(),
                "currents" => $entitiesList->getListFilterCurrents()
            ],
            'sortedColumn' => $entitiesList->getSortedColumn(),
            'sortedDirection' => $entitiesList->getSortedDirection()
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
    public function duplicateEntity($categoryName, $entityName, $id, $lang = null)
    {
        if ($lang) {
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
     * @param Request $request
     * @return mixed
     */
    public function updateEntity($categoryName, $entityName, $id, Request $request)
    {
        return $this->save($categoryName, $entityName, $request, $id);
    }

    /**
     * Create an entity.
     *
     * @param $categoryName
     * @param $entityName
     * @param Request $request
     * @return mixed
     */
    public function storeEntity($categoryName, $entityName, Request $request)
    {
        return $this->save($categoryName, $entityName, $request, null);
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
     * @param Request $request
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    public function ax_reorderEntities($categoryName, $entityName, Request $request)
    {
        $entities = $request->get("entities");

        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = app($entity->repository);

        // Reorder
        $repo->reorder($entities);

        return response()->json(["ok" => true]);
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
        $repo = app($entity->repository);

        $repo->delete($id);

        return redirect()->back();

        //return redirect()->route("cms.list", [$categoryName, $entityName]);
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

        return redirect()->back();
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param $fieldName
     * @param Request $request
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    public function ax_customSearchField($categoryName, $entityName, $fieldName, Request $request)
    {
        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = app($entity->repository);

        return response()->json(CustomSearchField::renderCustomSearch($fieldName, $repo, $request));
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
        $repo = app($entity->repository);

        // Activate / deactivate
        $activate ? $repo->activate($id) : $repo->deactivate($id);

        return response()->json(["ok" => true]);
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param $id
     * @param bool $duplication
     * @throws \Dvlpp\Sharp\Exceptions\InstanceNotFoundException
     * @return mixed
     */
    private function form($categoryName, $entityName, $id, $duplication = false)
    {
        $creation = ($id === null);

        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = app($entity->repository);

        // Retrieve the corresponding DB entity
        $instance = $creation ? $repo->newInstance() : $repo->find($id);

        if ($instance) {
            if (session()->has('masterInstanceData')) {
                // We are back from a embedded entity form.
                // We have to repopulate the master form (this form) as it was before
                $formOldDataStr = unserialize(Session::get('masterInstanceData'));
                session()->flashInput($formOldDataStr);
            }

            // Duplication management: we simply add an attribute here
            $instance->__sharp_duplication = $duplication;

            if ($duplication && method_exists($repo, "prepareForDuplication")) {
                // We call the repository hook for duplication, in case there's some
                // ajusts to make on the instance
                $instance = $repo->prepareForDuplication($instance);
            }

            // And return the View
            return view('sharp::cms.entityForm', [
                'instance' => $instance,
                'entity' => $entity,
                'category' => SharpCmsConfig::findCategory($categoryName)
            ]);
        }

        throw new InstanceNotFoundException("Instance of id [$id] and type [$categoryName.$entityName] can't be found");

    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    private function save($categoryName, $entityName, Request $request, $id)
    {
        $creation = ($id === null);

        $data = $request->all();

        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = app($entity->repository);

        try {
            // First : validation
            if ($entity->validator) {
                $validator = app($entity->validator);
                $validator->validate($data, $id);
            }

            // Then : update (calling repo)
            if ($creation) {
                $repo->create($data);
            } else {
                $repo->update($id, $data);
            }

            // And redirect
            return redirect()->route("cms.list", [$categoryName, $entityName]);

        } catch (ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->getErrors());
        }
    }

    private function changeLang($lang)
    {
        $languages = SharpSiteConfig::getLanguages();

        if ($languages) {
            if (!$lang || !array_key_exists($lang, $languages)) {
                $lang = array_values($languages)[0];
            }

            session()->put("sharp_lang", $lang);
        }
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param Request $request
     * @return bool
     */
    protected function restoreQuerystringForListEntities($categoryName, $entityName, Request $request)
    {
        $sessionQs = session("listViewInput_{$categoryName}_{$entityName}");

        if(!sizeof($request->all()) && $sessionQs) {
            foreach($sessionQs as $param=>$value) {
                if(!is_null($value)) return $sessionQs;
            }
        }

        return false;
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param Request $request
     */
    protected function saveQuerystringForListEntities($categoryName, $entityName, Request $request)
    {
        session()->put("listViewInput_{$categoryName}_{$entityName}",
            $request->only(['page', 'sort', 'dir', 'search', 'sub']));
    }

} 