<?php

namespace Dvlpp\Sharp\Http;

use Dvlpp\Sharp\Exceptions\InvalidStateException;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;
use Illuminate\Http\Request;
use Dvlpp\Sharp\Config\SharpConfig;
use Illuminate\Contracts\Auth\Access\Gate;
use Dvlpp\Sharp\ListView\SharpEntitiesList;
use Dvlpp\Sharp\Http\Utils\CheckAbilityTrait;
use Dvlpp\Sharp\Form\Fields\CustomSearchField;
use Dvlpp\Sharp\Exceptions\ValidationException;
use Dvlpp\Sharp\Exceptions\InstanceNotFoundException;

/**
 * Class CmsController
 */
class EntityController extends Controller
{
    use CheckAbilityTrait;

    /**
     * @var Gate
     */
    private $gate;

    /**
     * CmsController constructor.
     * @param Gate $gate
     */
    public function __construct(Gate $gate)
    {
        parent::__construct();
        $this->gate = $gate;
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
    public function index($categoryName, $entityName, Request $request)
    {
        $this->checkAbility('list', $categoryName, $entityName);

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
        $entity = SharpConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = app($entity->repository);

        // Grab entities (input is managed there, for search, pagination, ...)
        $entitiesList = (new SharpEntitiesList($entity, $repo, $request))->execute();

        // And return the View
        return view('sharp::cms.entitiesList', [
            'category' => SharpConfig::findCategory($categoryName),
            'entity' => $entity,
            'list' => $entitiesList
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
    public function edit($categoryName, $entityName, $id)
    {
        $this->checkAbility('update', $categoryName, $entityName, $id);

        return $this->form($categoryName, $entityName, $id);
    }

    /**
     * Show create form of an entity.
     *
     * @param $categoryName
     * @param $entityName
     * @return mixed
     */
    public function create($categoryName, $entityName)
    {
        $this->checkAbility('create', $categoryName, $entityName);

        return $this->form($categoryName, $entityName, null);
    }

    /**
     * Show duplicate form of an entity.
     *
     * @param $categoryName
     * @param $entityName
     * @param $id
     * @throws InstanceNotFoundException
     * @return mixed
     */
    public function duplicate($categoryName, $entityName, $id)
    {
        $this->checkAbility('duplicate', $categoryName, $entityName, $id);

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
    public function update($categoryName, $entityName, $id, Request $request)
    {
        $this->checkAbility('update', $categoryName, $entityName, $id);

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
    public function store($categoryName, $entityName, Request $request)
    {
        $this->checkAbility('create', $categoryName, $entityName);

        return $this->save($categoryName, $entityName, $request, null);
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param Request $request
     * @return mixed
     */
    public function changeState($categoryName, $entityName, Request $request)
    {
        $state = $request->get("state");
        $instanceId = $request->get("instance");
        if($state === null || $instanceId === null) abort(403);

        $this->checkAbility("changeState-$state", $categoryName, $entityName, $instanceId);

        try {
            $state = $this->entityRepository($categoryName, $entityName)
                ->changeState($instanceId, $state);

            return response()->json(["state" => $state], 200);

        } catch(InvalidStateException $e) {
            return response()->json([
                "error" => trans("sharp::ui.list_entityChangeStateError"),
                "message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param Request $request
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    public function reorder($categoryName, $entityName, Request $request)
    {
        $this->checkAbility('reorder', $categoryName, $entityName);

        $entities = $request->get("entities");

        // Reorder
        $this->entityRepository($categoryName, $entityName)->reorder($entities);

        return response()->json(["ok" => true]);
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @param $id
     * @return mixed
     */
    public function destroy($categoryName, $entityName, $id)
    {
        $this->checkAbility('delete', $categoryName, $entityName, $id);

        // Find Entity config (from sharp CMS config file)
        $entity = SharpConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = app($entity->repository);

        $this->fireEvent($entity, "beforeDelete", compact('id'));

        $repo->delete($id);

        $this->fireEvent($entity, "afterDelete", compact('id'));

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
        return response()->json(CustomSearchField::renderCustomSearch(
            $fieldName,
            $this->entityRepository($categoryName, $entityName),
            $request)
        );
    }

    /**
     * @param $categoryName
     * @param $entityName
     * @return SharpCmsRepository
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    private function entityRepository($categoryName, $entityName)
    {
        $entity = SharpConfig::findEntity($categoryName, $entityName);

        return app($entity->repository);
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
        $entity = SharpConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = app($entity->repository);

        // Retrieve the corresponding DB entity
        $instance = $creation ? $repo->newInstance() : $repo->find($id);

        if ($instance) {
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
                'category' => SharpConfig::findCategory($categoryName)
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
        $entity = SharpConfig::findEntity($categoryName, $entityName);

        // Instantiate the entity repository
        $repo = app($entity->repository);

        try {
            $this->fireEvent($entity, "beforeValidate", compact('id', 'data'));

            // First validation
            if ($entity->validator) {
                $validator = app($entity->validator);
                $validator->validate($data, $id);
            }

            // Then update (calling repo)
            if ($creation) {
                $this->fireEvent($entity, "beforeCreate", compact('data'));
                $instance = $repo->create($data);

            } else {
                $this->fireEvent($entity, "beforeUpdate", compact('id', 'data'));
                $instance = $repo->update($id, $data);
            }

            $this->fireEvent($entity, "afterUpdate", compact('instance'));

            // And redirect
            return response()->json([
                "url"=>route("cms.list", [$categoryName, $entityName])
            ], 200);

        } catch (ValidationException $e) {
            return response()->json($e->getErrors(), 422);
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

    private function fireEvent($entityConfig, $eventName, $params)
    {
        if($entityConfig->events && $entityConfig->events->$eventName) {
            event(new $entityConfig->events->$eventName($params));
        }
    }

}