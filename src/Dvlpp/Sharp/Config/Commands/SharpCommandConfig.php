<?php

namespace Dvlpp\Sharp\Config\Commands;

use Dvlpp\Sharp\Config\SharpFormFieldConfig;

class SharpCommandConfig
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $handler;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $iconName;

    /**
     * @var string
     */
    protected $authLevel;

    /**
     * @var string
     */
    protected $confirmMessage;

    /**
     * @var array
     */
    protected $formFieldsConfig = [];

    /**
     * @var SharpCommandFormTemplateConfig
     */
    protected $formTemplateConfig;

    /**
     * @param string $key
     * @param string $label
     * @param string $handler
     * @return static
     */
    public static function create($key, $label, $handler)
    {
        $instance = new static;
        $instance->key = $key;
        $instance->label = $label;
        $instance->handler = $handler;

        return $instance;
    }

    /**
     * Set the icon name for the command.
     *
     * @param string $iconName
     * @return $this
     */
    public function setIcon($iconName)
    {
        $this->iconName = $iconName;

        return $this;
    }

    /**
     * Set the needed auth level to execute the command.
     *
     * @param string $authLevel
     * @return $this
     */
    public function setAuthLevel($authLevel)
    {
        $this->authLevel = $authLevel;

        return $this;
    }

    /**
     * Add a form field to the command to be displayed before execution.
     *
     * @param SharpFormFieldConfig $formFieldConfig
     * @return $this
     */
    public function addFormField(SharpFormFieldConfig $formFieldConfig)
    {
        $this->formFieldsConfig[] = $formFieldConfig;

        return $this;
    }

    public function setFormTemplate(SharpCommandFormTemplateConfig $formTemplateConfig)
    {
        $this->formTemplateConfig = $formTemplateConfig;

        return $this;
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
    public function handler()
    {
        return $this->handler;
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
    public function iconName()
    {
        return $this->iconName;
    }

    /**
     * @return array
     */
    public function formFieldsConfig()
    {
        return $this->formFieldsConfig;
    }

    /**
     * @return bool
     */
    public function hasForm()
    {
        return sizeof($this->formFieldsConfig) != 0;
    }

    /**
     * @return string
     */
    public function authLevel()
    {
        return $this->authLevel;
    }

    /**
     * @return SharpCommandFormTemplateConfig
     */
    public function formTemplateConfig()
    {
        if(!$this->formTemplateConfig) {
            // Generate a default form template, one field a row.
            $formTemplate = SharpCommandFormTemplateConfig::create();

            foreach($this->formFieldsConfig as $formFieldConfig) {
                $formTemplate->addField($formFieldConfig->key());
            }

            return $formTemplate;
        }

        return $this->formTemplateConfig;
    }

    /**
     * @return string
     */
    public function confirmMessage()
    {
        return $this->confirmMessage;
    }

    /**
     * @param string $confirmMessage
     */
    public function setConfirmMessage($confirmMessage)
    {
        $this->confirmMessage = $confirmMessage;
    }

    /**
     * @return bool
     */
    public function hasConfirmation()
    {
        return !is_null($this->confirmMessage)
            && strlen($this->confirmMessage);
    }
}