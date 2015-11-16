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
}