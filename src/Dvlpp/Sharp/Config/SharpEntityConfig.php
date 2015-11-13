<?php

namespace Dvlpp\Sharp\Config;

/**
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
    protected $paginable = false;

    /**
     * @var int|null
     */
    protected $paginationSize = 20;

    /**
     * @var string|null
     */
    protected $defaultSort = null;

    /**
     * @var array|null
     */
    protected $filters = null;

    /**
     * @var array
     */
    private $listTemplateColumnsConfig = [];

    /**
     * @var array
     */
    private $formFieldsConfig = [];

    /**
     * @var array
     */
    private $formTemplateColumnsConfig = [];

    /**
     * @var array
     */
    private $entityCommandsConfig = [];

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
}