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
    protected $subList;

    /**
     * @var integer
     */
    protected $count;

    /**
     * @var Paginator
     */
    protected $pagination;

    /**
     * @var string
     */
    protected $sortedColumn;

    /**
     * @var string
     */
    protected $sortedDirection;

    /**
     * @var string
     */
    protected $search;

    /**
     * @var \Dvlpp\Sharp\Config\Entities\SharpEntity
     */
    private $entity;
    /**
     * @var \Dvlpp\Sharp\Repositories\SharpCmsRepository
     */
    private $repo;

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
            $this->repo->initCurrentSublistId(Input::get("sub"));
            $this->subLists = $this->repo->getSublists();
            $this->subList = $this->repo->getCurrentSublistId();
        }
    }

    /**
     * @return mixed
     */
    public function getInstances()
    {
        // Manage column sort
        if($this->entity->list_template->sortable)
        {
            // First determine which column is sorted
            $this->sortedColumn = null;
            foreach($this->entity->list_template->columns as $colKey=>$col)
            {
                if($col->sortable
                    && ($colKey==Input::get("sort")
                        || (!Input::has("sort") && !$this->sortedColumn)))
                {
                    $this->sortedColumn = $colKey;
                    $this->sortedDirection = Input::get("dir") ?: "asc";
                    break;
                }
            }
        }

        // Manage search
        if($this->entity->list_template->searchable && Input::get("search"))
        {
            $this->search = urldecode(Input::get("search"));
        }

        // And finally grab the entities
        if($this->entity->list_template->paginate)
        {
            // Pagination config is set : grab the current page
            $pagination = $this->repo->paginate(
                $this->entity->list_template->paginate,
                $this->sortedColumn,
                $this->sortedDirection,
                $this->search);
            $this->count = $pagination->getTotal();
            $this->pagination = $pagination;
            return $pagination->getItems();
        }
        else
        {
            // Grab all entities of this kind from DB
            $instances = $this->repo->listAll($this->sortedColumn, $this->sortedDirection, $this->search);
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
     * @return string
     */
    public function getSortedColumn()
    {
        return $this->sortedColumn;
    }

    /**
     * @return string
     */
    public function getSortedDirection()
    {
        return $this->sortedDirection;
    }

    /**
     * @return int
     */
    public function getSublistId()
    {
        return $this->subList;
    }

    /**
     * @return array
     */
    public function getSublists()
    {
        return $this->subLists;
    }


}