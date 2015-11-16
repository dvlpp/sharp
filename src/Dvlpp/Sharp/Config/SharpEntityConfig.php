<?php

namespace Dvlpp\Sharp\Config;

use Dvlpp\Sharp\Config\Commands\SharpCommandConfig;

/**
 * TODO gérer les Filtres !
 * The sharp config base class, which every entity config must extend.
 *
 * Class SharpEntityConfig
 * @package Dvlpp\Sharp\Config
 */
abstract class SharpEntityConfig
{
    /**
     * @var string
     */
    protected $label = "[sharp]";

    /**
     * @var string
     */
    protected $icon = "page-o";

    /**
     * @var string
     */
    protected $plural = "[sharp]";

    /**
     * @var null|string
     */
    protected $repository = null;

    /**
     * @var null|string
     */
    protected $validator = null;

    /**
     * @var bool
     */
    protected $duplicable = false;

    /**
     * @var bool
     */
    protected $searchable = false;

    /**
     * @var bool
     */
    protected $pageable = false;

    /**
     * @var bool
     */
    protected $creatable = true;

    /**
     * @var bool
     */
    protected $reorderable = false;

    /**
     * @var int|null
     */
    protected $pageSize = 20;

    /**
     * @var string|null
     */
    protected $defaultSort = null;

    /**
     * @var array|null
     */
    protected $listFilters = null;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $categoryKey;

    /**
     * @var array
     */
    private $listTemplateColumnsConfig = null;

    /**
     * @var array
     */
    private $formFieldsConfig = null;

    /**
     * @var array
     */
    private $formTemplateColumnsConfig = null;

    /**
     * @var array
     */
    private $entityCommandsConfig = null;

    /**
     * @var array
     */
    private $listCommandsConfig = null;

    /**
     * Build the list template columns using addListColumn()
     *
     * @return void
     */
    abstract function buildListTemplate();

    /**
     * Build the fields for the form, using addFormField()
     *
     * @return void
     */
    abstract function buildFormFields();

    /**
     * Build the form template, using addFormColumn and addFormTab
     *
     * @return void
     */
    abstract function buildFormTemplate();

    /**
     * Build the entity commands, using addEntityCommand()
     *
     * @return void
     */
    function buildEntityCommands() {}

    /**
     * Build the list commands, using addListCommand()
     *
     * @return void
     */
    function buildListCommands() {}

    /**
     * @return SharpEntityStateIndicator|null
     */
    function stateIndicator() {
        return null;
    }

    /**
     * Add a column in the list template.
     *
     * @param SharpListTemplateColumnConfig $listTemplateColumnConfig
     */
    final function addListColumn(SharpListTemplateColumnConfig $listTemplateColumnConfig)
    {
        $this->listTemplateColumnsConfig[] = $listTemplateColumnConfig;
    }

    /**
     * Add a field in the form.
     *
     * @param SharpFormFieldConfig $formFieldConfig
     */
    final function addFormField(SharpFormFieldConfig $formFieldConfig)
    {
        $this->formFieldsConfig[] = $formFieldConfig;
    }

    /**
     * Add a field in the form.
     *
     * @param SharpFormTemplateColumnConfig $formTemplateColumnConfig
     */
    final function addFormTemplateColumn(SharpFormTemplateColumnConfig $formTemplateColumnConfig)
    {
        $this->formTemplateColumnsConfig[] = $formTemplateColumnConfig;
    }

    /**
     * Add an entity command.
     *
     * @param SharpCommandConfig $commandConfig
     */
    final function addEntityCommand(SharpCommandConfig $commandConfig)
    {
        $this->entityCommandsConfig[] = $commandConfig;
    }

    /**
     * Add an list command.
     *
     * @param SharpCommandConfig $commandConfig
     */
    final function addListCommand(SharpCommandConfig $commandConfig)
    {
        $this->listCommandsConfig[] = $commandConfig;
    }

    /**
     * @return null|string
     */
    public function repository()
    {
        return $this->repository;
    }

    /**
     * @return array
     */
    public function listTemplateColumnsConfig()
    {
        if(!$this->listTemplateColumnsConfig) {
            $this->buildListTemplate();
        }

        return (array) $this->listTemplateColumnsConfig;
    }

    /**
     * @return array
     */
    public function entityCommandsConfig()
    {
        if(!$this->entityCommandsConfig) {
            $this->buildEntityCommands();
        }

        return (array) $this->entityCommandsConfig;
    }

    /**
     * @return array
     */
    public function listCommandsConfig()
    {
        if(!$this->listCommandsConfig) {
            $this->buildListCommands();
        }

        return (array) $this->listCommandsConfig;
    }

    /**
     * @return bool
     */
    public function searchable()
    {
        return $this->searchable;
    }

    /**
     * @return bool
     */
    public function pageable()
    {
        return $this->pageable;
    }

    /**
     * @return int|null
     */
    public function pageSize()
    {
        return $this->pageSize;
    }

    /**
     * @return null|string
     */
    public function defaultSort()
    {
        return $this->defaultSort;
    }

    /**
     * @return array|null
     */
    public function listFilters()
    {
        return $this->listFilters;
    }

    /**
     * @return string
     */
    public function label()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function icon()
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function plural()
    {
        return $this->plural;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @param string $categoryKey
     */
    public function setCategoryKey($categoryKey)
    {
        $this->categoryKey = $categoryKey;
    }

    /**
     * @return string
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function categoryKey()
    {
        return $this->categoryKey;
    }

    /**
     * @return boolean
     */
    public function creatable()
    {
        return $this->creatable;
    }

    /**
     * @return boolean
     */
    public function reorderable()
    {
        return $this->reorderable;
    }

    /**
     * @return boolean
     */
    public function duplicable()
    {
        return $this->duplicable;
    }

}