<?php namespace Dvlpp\Sharp\Repositories\AutoUpdater\Valuators;

/**
 * Class SimpleValuator
 * @package Dvlpp\Sharp\Repositories\AutoUpdater\Valuators
 */
class SimpleValuator implements Valuator
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
        $this->instance->{$this->attr} = $this->data;
    }

} 