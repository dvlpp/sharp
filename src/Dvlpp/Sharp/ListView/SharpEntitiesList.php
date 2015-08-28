<?php namespace Dvlpp\Sharp\ListView;

use Dvlpp\Sharp\Config\Entities\SharpEntity;
use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;
use Dvlpp\Sharp\Repositories\SharpHasSublist;
use Dvlpp\Sharp\Repositories\SharpHasListFilters;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

/**
 * This class manages all the entities listing stuff, from pagination to sublist via sorting
 *
 * Class SharpEntitiesList
 * @package Dvlpp\Sharp\ListView
 */
class SharpEntitiesList
{

    /**
     * @var array
     * @deprecated
     */
    protected $subLists;

    /**
     * @var integer
     * @deprecated
     */
    protected $currentSubListId;

    /**
     * @var integer
     */
    protected $count;

    /**
     * @var Paginator
     */
    protected $pagination;

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
        $this->manageSublist();
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

    /**
     * Sublists are entities subsets. This method intents to grab all available sets
     * from the functional repo, as well as the current one.
     *
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException
     * @deprecated
     */
    private function manageSublist()
    {
        // Manage sublist if set in the config file.
        // Sublist is used to group entities. The project repository is responsible
        // of giving the current sublist (with a default) and the available ones.
        $subLists = null;

        if ($this->entity->list_template->sublist) {
            if (!$this->repo instanceof SharpHasSublist) {
                throw new MandatoryClassNotFoundException("Repository ["
                    . get_class($this->repo)
                    . "] has to implement \\Dvlpp\\Sharp\\Repositories\\SharpHasSublist in order to manage sublists");
            }

            // Init current subset in repo
            $this->repo->initCurrentSublistId($this->request->get("sub"));

            $this->currentSubListId = $this->repo->getCurrentSublistId();
            $this->subLists = $this->repo->getSublists();
        }
    }

    /**
     * Grab the actual instances.
     *
     * @return mixed
     */
    public function getInstances()
    {
        // First create the params object with stuff like search, sorting
        $this->createParams();

        // And finally grab the entities
        if ($this->entity->list_template->paginate) {
            // Pagination config is set: grab the current page
            $this->pagination = $this->repo->paginate($this->entity->list_template->paginate, $this->params);

            $this->count = $this->pagination->total();

            return $this->pagination->items();
        } else {
            // Grab all entities of this kind from DB
            $instances = $this->repo->listAll($this->params);
            $this->count = sizeof($instances);

            return $instances;
        }
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return Paginator
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @deprecated
     * @return array
     */
    public function getSublists()
    {
        return $this->subLists;
    }

    /**
     * @deprecated
     * @return array
     */
    public function getCurrentSublistId()
    {
        return $this->currentSubListId;
    }

    public function getListFilterCurrents()
    {
        return $this->listFilterCurrents;
    }

    public function getListFilterContents()
    {
        return $this->listFilterContents;
    }

    public function getSortedColumn()
    {
        return $this->params->getSortedColumn();
    }

    public function getSortedDirection()
    {
        return $this->params->getSortedDirection();
    }

    public function createParams()
    {
        $this->params = new SharpEntitiesListParams();

        if ($this->currentSubListId) {
            $this->params->setCurrentSubListId($this->getCurrentSublistId());
        }

        // Manage column sort: first determine which column is sorted
        list($sortCol, $sortDir) = $this->retrieveSorting();

        foreach ($this->entity->list_template->columns as $colKey => $col) {
            if ($col->sortable && (!$sortCol || $colKey == $sortCol)) {
                $this->params->setSortedColumn($colKey);
                $this->params->setSortedDirection($sortDir);
                break;
            }
        }

        // Manage search
        $search = null;
        if ($this->entity->list_template->searchable && $this->request->has("search")) {
            $search = urldecode($this->request->get("search"));
        }

        // Manage advanced search
        if (!$search && $this->entity->advanced_search->data && $this->request->has("adv")) {
            $this->params->setAdvancedSearch(true);
            $search = [];
            foreach ($this->request->all() as $input => $value) {
                if (!starts_with($input, "adv_")) {
                    continue;
                }
                if ((is_array($value) && !sizeof($value))
                    || !is_array($value) && !strlen(trim($value))
                ) {
                    continue;
                }

                if (is_array($value)) {
                    foreach ($value as $v) {
                        $search[substr($input, 4)][] = urldecode($v);
                    }
                } else {
                    $search[substr($input, 4)] = urldecode($value);
                }
            }
        }

        $this->params->setSearch($search);

        return $this->params;
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