<?php

namespace Dvlpp\Sharp\Repositories\AutoUpdater\Valuators;

/**
 * Class DateValuator
 * @package Dvlpp\Sharp\Repositories\AutoUpdater\Valuators
 */
class DateValuator implements Valuator
{

    /**
     * @var
     */
    private $instance;

    /**
     * @var string
     */
    private $attr;

    /**
     * @var string
     */
    private $data;

    /**
     * @param $instance
     * @param $attr
     * @param $data
     */
    function __construct($instance, $attr, $data)
    {
        $this->instance = $instance;
        $this->attr = $attr;
        $this->data = $data;
    }

    /**
     * Valuate the field
     */
    public function valuate()
    {
        // Workaround to remove unwanted part of the iso date format
        // added by browser
        if($pos = strpos($this->data, "(")) {
            $this->data = substr($this->data, 0, $pos);
        }
        
        $value = date("Y-m-d H:i:s", strtotime($this->data));

        $this->instance->{$this->attr} = $value;
    }

} 