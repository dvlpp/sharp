<?php

namespace Dvlpp\Sharp\Config;

use Dvlpp\Sharp\Config\Commands\SharpCommandConfig;
use Dvlpp\Sharp\Config\Utils\HasFormTemplateColumnTrait;

/**
 * The sharp config base class, which every entity config must extend.
 *
 * Class SharpEntityConfig
 * @package Dvlpp\Sharp\Config
 */
abstract class SharpEntityConfig
{
    use HasFormTemplateColumnTrait;

    const EVENT_BEFORE_VALIDATE = "beforeValidate";
    const EVENT_BEFORE_CREATE = "beforeCreate";
    const EVENT_BEFORE_UPDATE = "beforeUpdate";
    const EVENT_AFTER_UPDATE = "afterUpdate";
    const EVENT_BEFORE_DELETE = "beforeDelete";
    const EVENT_AFTER_DELETE = "afterDelete";

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
    protected $listFilters = false;

    /**
     * @var array|null
     */
    protected $eventsList = false;

    /**
     * @var string
     */
    protected $idAttribute = "id";

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
    private $formTemplateTabsConfig = null;

    /**
     * @var array
     */
    private $formFieldsConfig = null;

    /**
     * @var array
     */
    private $entityCommandsConfig = false;

    /**
     * @var array
     */
    private $listCommandsConfig = false;

    /**
     * @var SharpEntityStateIndicator
     */
    private $stateIndicator = false;

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
     * Build the entity commands, using addEntityCommand()
     *
     * @return void
     */
    function buildEntityCommands()
    {
        $this->entityCommandsConfig = null;
    }

    /**
     * Build the list commands, using addListCommand()
     *
     * @return void
     */
    function buildListCommands()
    {
        $this->listCommandsConfig = null;
    }

    /**
     * Build the list filters, using addListFilter()
     */
    function buildListFilters()
    {
        $this->listFilters = null;
    }

    /**
     * Build the entity state indicator, using setStateIndicator()
     */
    function buildStateIndicator()
    {
        $this->stateIndicator = null;
    }

    /**
     * Build the events list, using addEvent()
     */
    public function buildEventsList()
    {
        $this->eventsList = null;
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
        $formFieldConfig->setEntity($this);

        $this->formFieldsConfig[] = $formFieldConfig;
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
     * Add a list filter.
     *
     * @param string $name
     */
    final function addListFilter($name)
    {
        $this->listFilters[] = $name;
    }

    /**
     * Add an event.
     *
     * @param string $name
     * @param string $eventClass
     */
    final function addEvent($name, $eventClass)
    {
        $this->eventsList[$name][] = $eventClass;
    }

    /**
     * Set the entity state indicator.
     *
     * @param SharpEntityStateIndicator $stateIndicator
     */
    final function setStateIndicator(SharpEntityStateIndicator $stateIndicator)
    {
        $this->stateIndicator = $stateIndicator;
    }

    /**
     * @return null|string
     */
    public function repository()
    {
        return $this->repository;
    }

    /**
     * @return null|string
     */
    public function validator()
    {
        return $this->validator;
    }

    public function formFieldsConfig()
    {
        if(!$this->formFieldsConfig) {
            $this->buildFormFields();
        }

        return (array) $this->formFieldsConfig;
    }

    /**
     * @return SharpEntityStateIndicator|null
     */
    public function stateIndicator()
    {
        if($this->stateIndicator === false) {
            $this->buildStateIndicator();
        }

        return $this->stateIndicator;
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

    public function addFormTemplateTab(SharpFormTemplateTabConfig $formTemplateTabConfig)
    {
        $this->formTemplateTabsConfig[] = $formTemplateTabConfig;
    }

    public function formTemplateTabsConfig()
    {
        if(!$this->formFieldsConfig) {
            $this->buildFormFields();
        }

        return (array) $this->formTemplateTabsConfig;
    }

    /**
     * @return array
     */
    public function entityCommandsConfig()
    {
        if($this->entityCommandsConfig === false) {
            $this->buildEntityCommands();
        }

        return (array) $this->entityCommandsConfig;
    }

    /**
     * @return array
     */
    public function listCommandsConfig()
    {
        if($this->listCommandsConfig === false) {
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
     * @return array
     */
    public function listFilters()
    {
        if($this->listFilters === false) {
            $this->buildListFilters();
        }

        return (array) $this->listFilters;
    }

    /**
     * @return array
     */
    public function eventsList()
    {
        if($this->eventsList === false) {
            $this->buildEventsList();
        }

        return (array) $this->eventsList;
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

    /**
     * @param $commandKey
     * @return null|SharpCommandConfig
     */
    public function findListCommand($commandKey)
    {
        foreach($this->listCommandsConfig() as $listCommandConfig) {
            if($listCommandConfig->key() == $commandKey) return $listCommandConfig;
        }

        return null;
    }

    /**
     * @param $commandKey
     * @return null|SharpCommandConfig
     */
    public function findEntityCommand($commandKey)
    {
        foreach($this->entityCommandsConfig() as $entityCommandConfig) {
            if($entityCommandConfig->key() == $commandKey) return $entityCommandConfig;
        }

        return null;
    }

    /**
     * @return string
     */
    public function idAttribute()
    {
        return $this->idAttribute;
    }

    public function findField($key)
    {
        foreach($this->formFieldsConfig() as $formField) {
            if($formField->key() == $key) return $formField;
        }

        return null;
    }

}