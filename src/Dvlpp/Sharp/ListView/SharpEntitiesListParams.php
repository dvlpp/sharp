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
     * @var null
     */
    private $currentSublistId;

    /**
     * @param $sortedColumn
     * @param $sortedDirection
     * @param $search
     * @param null $currentSublistId
     */
    function __construct($sortedColumn, $sortedDirection, $search, $currentSublistId=null)
    {
        $this->sortedColumn = $sortedColumn;
        $this->sortedDirection = $sortedDirection;
        $this->search = $search;
        $this->currentSublistId = $currentSublistId;
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
     * @return string
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

    /**
     * @param string $termPrefix
     * @param string $termSuffix
     * @return array
     */
    public function getSearchTerms($termPrefix='%', $termSuffix='%')
    {
        $terms = [];
        foreach(explode(" ", $this->search) as $term)
        {
            $term = trim($term);
            if($term)
            {
                $terms[] = $termPrefix . $term . $termSuffix;
            }
        }
        return $terms;
    }
} 