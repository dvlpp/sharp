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
     * @param $sortedColumn
     * @param $sortedDirection
     * @param $search
     */
    function __construct($sortedColumn, $sortedDirection, $search)
    {
        $this->sortedColumn = $sortedColumn;
        $this->sortedDirection = $sortedDirection;
        $this->search = $search;
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

    public function getSearch()
    {
        return $this->search;
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