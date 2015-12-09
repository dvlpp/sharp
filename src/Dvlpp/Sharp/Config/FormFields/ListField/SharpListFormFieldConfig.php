<?php

namespace Dvlpp\Sharp\Config\FormFields\ListField;

use Dvlpp\Sharp\Config\SharpFormFieldConfig;

class SharpListFormFieldConfig extends SharpFormFieldConfig
{
    /**
     * @var bool
     */
    protected $addable;

    /**
     * @var bool
     */
    protected $sortable;

    /**
     * @var bool
     */
    protected $removable;

    /**
     * @var string
     */
    protected $orderAttribute;

    /**
     * @var string
     */
    protected $addButtonText;

    /**
     * @var string
     */
    protected $removeButtonText;

    /**
     * @var string
     */
    protected $itemIdAttribute;

    /**
     * @var array
     */
    protected $itemFormFieldsConfig = [];

    /**
     * @var SharpListItemFormTemplateConfig
     */
    protected $listItemFormTemplateConfig;

    /**
     * @param string $key
     * @return static
     */
    public static function create($key)
    {
        $instance = new static;
        $instance->key = $key;

        $instance->addable = false;
        $instance->sortable = false;
        $instance->removable = false;
        $instance->addButtonText = trans("sharp::ui.form_listField_addItem");
        $instance->removeButtonText = trans("sharp::ui.form_listField_deleteItem");

        $instance->label = "";

        return $instance;
    }

    /**
     * @param bool $addable
     * @return $this
     */
    public function setAddable($addable)
    {
        $this->addable = $addable;

        return $this;
    }

    /**
     * @param bool $sortable
     * @return $this
     */
    public function setSortable($sortable)
    {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * @param bool $removable
     * @return $this
     */
    public function setRemovable($removable)
    {
        $this->removable = $removable;

        return $this;
    }

    /**
     * @param string $orderAttribute
     * @return $this
     */
    public function setOrderAttribute($orderAttribute)
    {
        $this->orderAttribute = $orderAttribute;

        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setAddButtonText($text)
    {
        $this->addButtonText = $text;

        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setRemoveButtonText($text)
    {
        $this->removeButtonText = $text;

        return $this;
    }

    /**
     * Add a form field to the item of the list.
     *
     * @param SharpFormFieldConfig $formFieldConfig
     * @return $this
     */
    public function addItemFormField(SharpFormFieldConfig $formFieldConfig)
    {
        $this->itemFormFieldsConfig[] = $formFieldConfig;

        return $this;
    }

    public function type()
    {
        return "list";
    }

    /**
     * @return boolean
     */
    public function addable()
    {
        return $this->addable;
    }

    /**
     * @return boolean
     */
    public function sortable()
    {
        return $this->sortable;
    }

    /**
     * @return boolean
     */
    public function removable()
    {
        return $this->removable;
    }

    /**
     * @return string
     */
    public function orderAttribute()
    {
        return $this->orderAttribute;
    }

    /**
     * @return string
     */
    public function addButtonText()
    {
        return $this->addButtonText;
    }

    /**
     * @return string
     */
    public function removeButtonText()
    {
        return $this->removeButtonText;
    }

    /**
     * @return array
     */
    public function itemFormFieldsConfig()
    {
        return $this->itemFormFieldsConfig;
    }

    /**
     * @param string $itemIdAttribute
     * @return SharpListFormFieldConfig
     */
    public function setItemIdAttribute($itemIdAttribute)
    {
        $this->itemIdAttribute = $itemIdAttribute;

        return $this;
    }

    /**
     * @return string
     */
    public function itemIdAttribute()
    {
        return $this->itemIdAttribute;
    }

    public function setItemFormTemplate(SharpListItemFormTemplateConfig $listItemFormTemplateConfig)
    {
        $this->listItemFormTemplateConfig = $listItemFormTemplateConfig;

        return $this;
    }

    public function findItemField($itemKey)
    {
        foreach($this->itemFormFieldsConfig() as $itemFieldConfig) {
            if($itemFieldConfig->key() == $itemKey) {
                return $itemFieldConfig;
            }
        }

        return null;
    }

    /**
     * @return SharpListItemFormTemplateConfig
     */
    public function listItemFormTemplateConfig()
    {
        if(!$this->listItemFormTemplateConfig) {
            // Generate a default form template, one field a row.
            $formTemplate = SharpListItemFormTemplateConfig::create();

            foreach($this->itemFormFieldsConfig() as $formFieldConfig) {
                $formTemplate->addField($formFieldConfig->key());
            }

            return $formTemplate;
        }

        return $this->listItemFormTemplateConfig;
    }
}