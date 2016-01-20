<?php

namespace Dvlpp\Sharp\Config\FormFields;

use Dvlpp\Sharp\Config\SharpFormFieldConfig;

class SharpCustomSearchFormFieldConfig extends SharpFormFieldConfig
{
    /**
     * @var string
     */
    protected $idAttribute;

    /**
     * @var string
     */
    protected $listItemTemplate;

    /**
     * @var string
     */
    protected $resultTemplate;

    /**
     * @var int
     */
    protected $searchMinChar;

    /**
     * @var string
     */
    protected $modalTitle;


    /**
     * @param string $key
     * @param string $idAttribute
     * @param string $listItemTemplate
     * @param string $resultTemplate
     * @return static
     */
    public static function create($key, $idAttribute, $listItemTemplate, $resultTemplate)
    {
        $instance = new static;
        $instance->key = $key;
        $instance->idAttribute = $idAttribute;
        $instance->listItemTemplate = $listItemTemplate;
        $instance->resultTemplate = $resultTemplate;

        $instance->label = "";

        return $instance;
    }

    public function type()
    {
        return "customSearch";
    }

    /**
     * @return string
     */
    public function modalTitle()
    {
        return $this->modalTitle;
    }

    /**
     * @param string $modalTitle
     * @return $this
     */
    public function setModalTitle($modalTitle)
    {
        $this->modalTitle = $modalTitle;

        return $this;
    }

    /**
     * @return int
     */
    public function searchMinChar()
    {
        return $this->searchMinChar;
    }

    /**
     * @param int $searchMinChar
     * @return $this
     */
    public function setSearchMinChar($searchMinChar)
    {
        $this->searchMinChar = $searchMinChar;

        return $this;
    }

    /**
     * @return string
     */
    public function resultTemplate()
    {
        return $this->resultTemplate;
    }

    /**
     * @return string
     */
    public function listItemTemplate()
    {
        return $this->listItemTemplate;
    }

    /**
     * @return string
     */
    public function idAttribute()
    {
        return $this->idAttribute;
    }
}