<?php

namespace Dvlpp\Sharp\Config;

class SharpEntityStateIndicator
{
    /**
     * @var string
     */
    protected $stateAttributeName;

    /**
     * @var array
     */
    protected $states;

    /**
     * @var string
     */
    protected $visibilityConditionalAttribute;

    /**
     * @param string $stateAttributeName
     * @return static
     */
    public static function create($stateAttributeName)
    {
        $instance = new static;
        $instance->stateAttributeName = $stateAttributeName;

        return $instance;
    }

    /**
     * @param string $value
     * @param string $label
     * @param string $hexColor
     * @return $this
     */
    public function addState($value, $label, $hexColor)
    {
        $this->states[] = (object) [
            "value" => $value,
            "label" => $label,
            "hexColor" => $hexColor,
        ];

        return $this;
    }

    /**
     * Set the visibility of the state indicator per instance
     *
     * @param $conditionalAttribute string: must refer to an existing attribute of the entity. May start with a ! for negative.
     * @return $this
     */
    public function setVisibility($conditionalAttribute)
    {
        $this->visibilityConditionalAttribute = $conditionalAttribute;

        return $this;
    }

    /**
     * @return array
     */
    public function states()
    {
        return $this->states;
    }

    public function stateAttribute()
    {
        return $this->stateAttributeName;
    }

    public function findState($value)
    {
        foreach($this->states as $state) {
            if($state->value == $value) return $state;
        }

        return null;
    }

    /**
     * Checks if the state indicator should be shown
     * for the given $entityInstance
     *
     * @param $entityInstance
     * @return bool
     */
    public function isVisibleFor($entityInstance)
    {
        if(!$this->visibilityConditionalAttribute) {
            // No condition
            return true;
        }

        $isNegative = starts_with($this->visibilityConditionalAttribute, "!");
        $attribute = $isNegative
            ? substr($this->visibilityConditionalAttribute, 1)
            : $this->visibilityConditionalAttribute;

        $attributeValue = !! get_entity_attribute_value($entityInstance, $attribute);

        return $isNegative ? !$attributeValue : $attributeValue;
    }
}