<?php namespace Dvlpp\Sharp\ListView;

/**
 * Class SharpEntitiesListParams
 * @package Dvlpp\Sharp\ListView
 */
class SharpEntitiesListParams {

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
     * @var boolean
     */
    protected $isAdvancedSearch = false;

    /**
     * @var null
     */
    private $currentSublistId;


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
     * @return string|array
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @return string
     */
    public function getCurrentSublistId()
    {
        return $this->currentSublistId;
    }

    public function isAdvancedSearch()
    {
        return $this->isAdvancedSearch;
    }

    /**
     * @param string $currentSublistId
     */
    public function setCurrentSublistId($currentSublistId)
    {
        $this->currentSublistId = $currentSublistId;
    }

    /**
     * @param string $search
     */
    public function setSearch($search)
    {
        $this->search = $search;
    }

    /**
     * @param string $sortedColumn
     */
    public function setSortedColumn($sortedColumn)
    {
        $this->sortedColumn = $sortedColumn;
    }

    /**
     * @param string $sortedDirection
     */
    public function setSortedDirection($sortedDirection)
    {
        $this->sortedDirection = $sortedDirection;
    }

    public function setAdvancedSearch($isAdvanced)
    {
        $this->isAdvancedSearch = $isAdvanced;
    }

    public function getAdvancedSearchValue($field)
    {
        $value = null;

        if($this->isAdvancedSearch)
        {
            $index = null;
            if(strpos($field, ".") !== false)
            {
                list($field, $index) = explode(".", $field);
            }

            if(array_key_exists($field, $this->search))
            {
                return $index === null ? $this->search[$field] : $this->search[$field][$index];
            }
        }

        return $value;
    }

} 