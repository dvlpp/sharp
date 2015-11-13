<?php

namespace Dvlpp\Sharp\Config;

class SharpCommandConfig
{
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
     * @var array
     */
    protected $formFieldsConfig = [];

    /**
     * @param string $label
     * @param string $handler
     * @return static
     */
    public static function create($label, $handler)
    {
        $instance = new static;
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
}