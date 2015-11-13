<?php

namespace Dvlpp\Sharp\Config\FormFields;

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
     * @var array
     */
    protected $itemFormFieldsConfig = [];

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
        $instance->addButtonText = trans("sharp.ui.form_listField_addItem");
        $instance->removeButtonText = trans("sharp.ui.form_listField_deleteItem");

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
}