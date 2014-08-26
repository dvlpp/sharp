<?php namespace Dvlpp\Sharp\ListView;


use Dvlpp\Sharp\Config\Entities\SharpEntity;
use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;
use Illuminate\Pagination\Paginator;
use Input;

/**
 * This class manages all the entities listing stuff, from pagination to sublist via sorting
 *
 * Class SharpEntitiesList
 * @package Dvlpp\Sharp\ListView
 */
class SharpEntitiesList {

    /**
     * @var array
     */
    protected $subLists;

    /**
     * @var integer
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
     * @param SharpEntity $entity
     * @param SharpCmsRepository $repo
     */
    public function __construct(SharpEntity $entity, SharpCmsRepository $repo)
    {
        $this->entity = $entity;
        $this->repo = $repo;

        $this->manageSublist();
    }

    /**
     * Sublists are entities subsets. This method intents to grab all available sets
     * from the functional repo, as well as the current one.
     *
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException
     */
    private function manageSublist()
    {
        // Manage sublist if set in the config file.
        // Sublist is used to group entities. The project repository is responsible
        // of giving the current sublist (with a default) and the available ones.
        $subLists = null;
        if($this->entity->list_template->sublist)
        {
            if(!$this->repo instanceof \Dvlpp\Sharp\Repositories\SharpHasSublist)
            {
                throw new MandatoryClassNotFoundException("Repository ["
                    .get_class($this->repo)
                    ."] has to implements \\Dvlpp\\Sharp\\Repositories\\SharpHasSublist in order to manage sublists");
            }

            // Init current subset in repo
            $this->repo->initCurrentSublistId(Input::get("sub"));

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
        // Manage column sort
        $sortedColumn = null;
        $sortedDirection = null;

        // First determine which column is sorted
        foreach($this->entity->list_template->columns as $colKey=>$col)
        {
            if($col->sortable && ($colKey==Input::get("sort") || !Input::has("sort")))
            {
                $sortedColumn = $colKey;
                $sortedDirection = Input::get("dir") ?: "asc";
                break;
            }
        }

        // Manage search
        $search = null;

        if($this->entity->list_template->searchable && Input::get("search"))
        {
            $search = urldecode(Input::get("search"));
        }

        // Create the params object
        $this->params = new SharpEntitiesListParams($sortedColumn, $sortedDirection, $search);

        // And finally grab the entities
        if($this->entity->list_template->paginate)
        {
            // Pagination config is set: grab the current page
            $pagination = $this->repo->paginate($this->entity->list_template->paginate, $this->params);

            $this->count = $pagination->getTotal();
            $this->pagination = $pagination;

            return $pagination->getItems();
        }
        else
        {
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

    public function getSublists()
    {
        return $this->subLists;
    }

    public function getCurrentSublistId()
    {
        return $this->currentSubListId;
    }

    public function getSortedColumn()
    {
        return $this->params->getSortedColumn();
    }

    public function getSortedDirection()
    {
        return $this->params->getSortedDirection();
    }


}