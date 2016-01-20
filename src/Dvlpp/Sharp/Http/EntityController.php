<?php

namespace Dvlpp\Sharp\Http;

use Dvlpp\Sharp\Config\SharpEntityConfig;
use Dvlpp\Sharp\Exceptions\InstanceNotFoundException;
use Dvlpp\Sharp\Exceptions\InvalidStateException;
use Dvlpp\Sharp\Exceptions\ValidationException;
use Dvlpp\Sharp\Form\Fields\CustomSearchField;
use Dvlpp\Sharp\Http\Utils\CheckAbilityTrait;
use Dvlpp\Sharp\ListView\SharpEntitiesList;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Http\Request;

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
     * @param string $categoryKey
     * @param string $entityKey
     * @param Request $request
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    public function index($categoryKey, $entityKey, Request $request)
    {
        $this->checkAbility('list', $categoryKey, $entityKey);

        if ($qs = $this->restoreQuerystringForListEntities($categoryKey, $entityKey, $request)) {
            // We saved an old "input", which means we need to display the list
            // with some pagination, or sorting, or search config. We simply redirect
            // with the correct querystring based on old input
            return redirect()->route('cms.list',
                array_merge(["category" => $categoryKey, "entity" => $entityKey], $qs));

        } else {
            // Save input (we can use it later, see up)
            $this->saveQuerystringForListEntities($categoryKey, $entityKey, $request);
        }

        // Find Entity config (from sharp CMS config file)
        $entity = sharp_entity($categoryKey, $entityKey);

        // Instantiate the entity repository
        $repo = app($entity->repository());

        // Grab entities (input is managed there, for search, pagination, ...)
        $entitiesList = (new SharpEntitiesList($entity, $repo, $request))->execute();

        // And return the View
        return view('sharp::cms.entitiesList', [
            'category' => sharp_category($categoryKey),
            'entity' => $entity,
            'list' => $entitiesList
        ]);
    }

    /**
     * Show edit form of an entity.
     *
     * @param string $categoryKey
     * @param string $entityKey
     * @param $id
     * @return mixed
     */
    public function edit($categoryKey, $entityKey, $id)
    {
        $this->checkAbility('update', $categoryKey, $entityKey, $id);

        return $this->form($categoryKey, $entityKey, $id);
    }

    /**
     * Show create form of an entity.
     *
     * @param string $categoryKey
     * @param string $entityKey
     * @return mixed
     */
    public function create($categoryKey, $entityKey)
    {
        $this->checkAbility('create', $categoryKey, $entityKey);

        return $this->form($categoryKey, $entityKey, null);
    }

    /**
     * Show duplicate form of an entity.
     *
     * @param string $categoryKey
     * @param string $entityKey
     * @param string $instanceId
     * @throws InstanceNotFoundException
     * @return mixed
     */
    public function duplicate($categoryKey, $entityKey, $instanceId)
    {
        $this->checkAbility('duplicate', $categoryKey, $entityKey, $instanceId);

        return $this->form($categoryKey, $entityKey, $instanceId, true);
    }

    /**
     * Updates an entity.
     *
     * @param string $categoryKey
     * @param string $entityKey
     * @param string $instanceId
     * @param Request $request
     * @return mixed
     */
    public function update($categoryKey, $entityKey, $instanceId, Request $request)
    {
        $this->checkAbility('update', $categoryKey, $entityKey, $instanceId);

        return $this->save($categoryKey, $entityKey, $request, $instanceId);
    }

    /**
     * Create an entity.
     *
     * @param string $categoryKey
     * @param string $entityKey
     * @param Request $request
     * @return mixed
     */
    public function store($categoryKey, $entityKey, Request $request)
    {
        $this->checkAbility('create', $categoryKey, $entityKey);

        return $this->save($categoryKey, $entityKey, $request, null);
    }

    /**
     * @param string $categoryKey
     * @param string $entityKey
     * @param Request $request
     * @return mixed
     */
    public function changeState($categoryKey, $entityKey, Request $request)
    {
        $state = $request->get("state");
        $instanceId = $request->get("instance");
        if($state === null || $instanceId === null) abort(403);

        $this->checkAbility("changeState-$state", $categoryKey, $entityKey, $instanceId);

        try {
            $state = $this->entityRepository($categoryKey, $entityKey)
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
     * @param string $categoryKey
     * @param string $entityKey
     * @param Request $request
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    public function reorder($categoryKey, $entityKey, Request $request)
    {
        $this->checkAbility('reorder', $categoryKey, $entityKey);

        $entities = $request->get("entities");

        // Reorder
        $this->entityRepository($categoryKey, $entityKey)->reorder($entities);

        return response()->json(["ok" => true]);
    }

    /**
     * @param string $categoryKey
     * @param string $entityKey
     * @param string $instanceId
     * @return mixed
     */
    public function destroy($categoryKey, $entityKey, $instanceId)
    {
        $this->checkAbility('delete', $categoryKey, $entityKey, $instanceId);

        // Find Entity config (from sharp CMS config file)
        $entity = sharp_entity($categoryKey, $entityKey);

        // Instantiate the entity repository
        $repo = app($entity->repository());

        $this->fireEvent($entity, "beforeDelete", compact('id'));

        $repo->delete($instanceId);

        $this->fireEvent($entity, "afterDelete", compact('id'));

        return redirect()->back();
    }

    /**
     * @param string $categoryKey
     * @param string $entityKey
     * @param string $fieldName
     * @param Request $request
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    public function ax_customSearchField($categoryKey, $entityKey, $fieldName, Request $request)
    {
        return response()->json(CustomSearchField::renderCustomSearch(
            $fieldName,
            $this->entityRepository($categoryKey, $entityKey),
            $request)
        );
    }

    /**
     * @param string $categoryKey
     * @param string $entityKey
     * @return SharpCmsRepository
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    private function entityRepository($categoryKey, $entityKey)
    {
        $entity = sharp_entity($categoryKey, $entityKey);

        return app($entity->repository());
    }

    /**
     * @param string $categoryKey
     * @param string $entityKey
     * @param string $instanceId
     * @param bool $duplication
     * @throws \Dvlpp\Sharp\Exceptions\InstanceNotFoundException
     * @return mixed
     */
    private function form($categoryKey, $entityKey, $instanceId, $duplication = false)
    {
        $creation = ($instanceId === null);

        // Find Entity config (from sharp CMS config file)
        $entity = sharp_entity($categoryKey, $entityKey);

        // Instantiate the entity repository
        $repo = app($entity->repository());

        // Retrieve the corresponding DB entity
        $instance = $creation ? $repo->newInstance() : $repo->find($instanceId);

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
                'category' => sharp_category($categoryKey)
            ]);
        }

        throw new InstanceNotFoundException("Instance of id [$instanceId] and type [$categoryKey.$entityKey] can't be found");
    }

    /**
     * @param string $categoryKey
     * @param string $entityKey
     * @param Request $request
     * @param string $instanceId
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    private function save($categoryKey, $entityKey, Request $request, $instanceId)
    {
        $creation = ($instanceId === null);

        $data = $request->all();

        // Find Entity config (from sharp CMS config file)
        $entity = sharp_entity($categoryKey, $entityKey);

        // Instantiate the entity repository
        $repo = app($entity->repository());

        try {
            $this->fireEvent($entity, "beforeValidate", compact('id', 'data'));

            // First validation
            if ($entity->validator()) {
                $validator = app($entity->validator());
                $validator->validate($data, $instanceId);
            }

            // Then update (calling repo)
            if ($creation) {
                $this->fireEvent($entity, "beforeCreate", compact('data'));
                $instance = $repo->create($data);

            } else {
                $this->fireEvent($entity, "beforeUpdate", compact('id', 'data'));
                $instance = $repo->update($instanceId, $data);
            }

            $this->fireEvent($entity, "afterUpdate", compact('instance'));

            // And redirect
            return response()->json([
                "url"=>route("cms.list", [$categoryKey, $entityKey])
            ], 200);

        } catch (ValidationException $e) {
            $this->formatValidationErrors($e->getErrors());

            return response()->json($e->getErrors(), 422);
        }
    }

    /**
     * @param string $categoryKey
     * @param string $entityKey
     * @param Request $request
     * @return bool
     */
    protected function restoreQuerystringForListEntities($categoryKey, $entityKey, Request $request)
    {
        $sessionQs = session("listViewInput_{$categoryKey}_{$entityKey}");

        if(!sizeof($request->all()) && $sessionQs) {
            foreach($sessionQs as $param=>$value) {
                if(!is_null($value)) return $sessionQs;
            }
        }

        return false;
    }

    /**
     * @param string $categoryKey
     * @param string $entityKey
     * @param Request $request
     */
    protected function saveQuerystringForListEntities($categoryKey, $entityKey, Request $request)
    {
        session()->put("listViewInput_{$categoryKey}_{$entityKey}",
            $request->only(['page', 'sort', 'dir', 'search', 'sub']));
    }

    /**
     * @param SharpEntityConfig $entityConfig
     * @param string $eventName
     * @param array $params
     */
    private function fireEvent($entityConfig, $eventName, $params)
    {
        foreach($entityConfig->eventsList() as $eventKey => $listeners) {
            if($eventKey == $eventName) {
                foreach($listeners as $listener) {
                    event(new $listener($params));
                }
            }
        }
    }

    /**
     * Format validation errors, handling ~ special case.
     *
     * @param MessageBag $validationErrors
     */
    private function formatValidationErrors(MessageBag $validationErrors)
    {
        $errors = [];
        foreach($validationErrors->keys() as $key) {
            $errors[str_replace("~", "-", $key)] = $validationErrors->get($key);
        }
        if(count($errors)) {
            $validationErrors->merge($errors);
        }
    }

}