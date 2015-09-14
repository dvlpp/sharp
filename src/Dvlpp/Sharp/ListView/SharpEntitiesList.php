<?php namespace Dvlpp\Sharp\ListView;

use Dvlpp\Sharp\Config\Entities\SharpEntity;
use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;
use Dvlpp\Sharp\Repositories\SharpHasListFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

/**
 * This class manages all the entities listing stuff, from pagination to sublist via sorting
 *
 * Class SharpEntitiesList
 * @package Dvlpp\Sharp\ListView
 */
class SharpEntitiesList
{

    /**
     * @var LengthAwarePaginator
     */
    protected $paginator;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var array
     */
    protected $instances;

    /**
     * @var \Dvlpp\Sharp\Config\Entities\SharpEntity
     */
    private $entity;

    /**
     * @var \Dvlpp\Sharp\Repositories\SharpCmsRepository
     */
    private $repo;

    /**
     * @var \Dvlpp\Sharp\ListView\SharpEntitiesListParams
     */
    private $params;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var array
     */
    private $listFilterCurrents = [];

    /**
     * @var array
     */
    private $listFilterContents = [];

    /**
     * @param SharpEntity $entity
     * @param SharpCmsRepository $repo
     */
    public function __construct(SharpEntity $entity, SharpCmsRepository $repo, Request $request)
    {
        $this->entity = $entity;
        $this->repo = $repo;
        $this->request = $request;

        $this->manageFilters();
    }

    /**
     * Gets the instances
     */
    public function execute()
    {
        // First create the params object with stuff like search, sorting
        $this->createParams();

        // And finally grab the entities
        if ($this->entity->list_template->paginate) {
            // Pagination config is set: grab the current page
            $this->paginator = $this->repo->paginate($this->entity->list_template->paginate, $this->params);

            $this->count = $this->paginator->total();
            $this->instances = $this->paginator->items();

        } else {
            // Grab all entities of this kind from DB
            $this->instances = $this->repo->listAll($this->params);
            $this->count = sizeof($this->instances);
        }

        return $this;
    }

    /**
     * Grab the actual instances.
     *
     * @return mixed
     */
    public function instances()
    {
        return $this->instances;
    }


    /**
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * @return LengthAwarePaginator
     */
    public function paginator()
    {
        return $this->paginator;
    }

    public function listFilterCurrents()
    {
        return $this->listFilterCurrents;
    }

    public function listFilterContents()
    {
        return $this->listFilterContents;
    }

    public function sortedColumn()
    {
        return $this->params->sortedColumn;
    }

    public function sortedDirection()
    {
        return $this->params->sortedDirection;
    }

    public function createParams()
    {
        $this->params = new SharpEntitiesListParams();

        // Manage column sort: first determine which column is sorted
        list($sortCol, $sortDir) = $this->retrieveSorting();

        foreach ($this->entity->list_template->columns as $colKey => $col) {
            if ($col->sortable && (!$sortCol || $colKey == $sortCol)) {
                $this->params->sortedColumn = $colKey;
                $this->params->sortedDirection = $sortDir;
                break;
            }
        }

        // Manage search
        if ($this->entity->list_template->searchable && $this->request->has("search")) {
            $this->params->search = urldecode($this->request->get("search"));
        }

        return $this->params;
    }

    /**
     * Sublists are entities subsets. This method intents to grab all available sets
     * from the functional repo, as well as the current one.
     *
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException
     */
    private function manageFilters()
    {
        if ($this->entity->list_template->list_filters) {
            if (!$this->repo instanceof SharpHasListFilters) {
                throw new MandatoryClassNotFoundException("Repository ["
                    . get_class($this->repo)
                    . "] has to implement \\Dvlpp\\Sharp\\Repositories\\SharpHasListFilters in order to manage sublists");
            }

            // Init each list filter from request (if set)
            if ($this->request->get("sub")) {
                // params is composed this way: filter_key.filter_value
                $pos = strpos($this->request->get("sub"), ".");
                $this->repo->initListFilterIdFor(
                    substr($this->request->get("sub"), 0, $pos), // filter_key, as defined in config
                    substr($this->request->get("sub"), $pos+1) // filter_value
                );
            }

            $this->listFilterCurrents = (array)$this->repo->getListFilterCurrents();
            $this->listFilterContents = (array)$this->repo->getListFilterContents();
        }
    }

    private function retrieveSorting()
    {
        $sortCol = $this->request->has("sort") ? $this->request->get("sort") : null;
        $sortDir = $this->request->has("dir") ? $this->request->get("dir") : 'asc';

        if (!$sortCol && $this->entity->list_template->sort_default) {
            if (strpos($this->entity->list_template->sort_default, ':')) {
                list($sortCol, $sortDir) = explode(":", $this->entity->list_template->sort_default);
            } else {
                $sortCol = $this->entity->list_template->sort_default;
            }
        }

        return [$sortCol, $sortDir];
    }


}