<?php

namespace Dvlpp\Sharp\Config\FormFields;

use Carbon\Carbon;
use Dvlpp\Sharp\Config\SharpFormFieldConfig;

class SharpDateFormFieldConfig extends SharpFormFieldConfig
{

    /**
     * @var bool
     */
    protected $hasDate = true;

    /**
     * @var bool
     */
    protected $hasTime = false;

    /**
     * @var Carbon
     */
    protected $minDate;

    /**
     * @var Carbon
     */
    protected $maxDate;

    /**
     * @var Carbon
     */
    protected $minTime;

    /**
     * @var Carbon
     */
    protected $maxTime;

    /**
     * @var int
     */
    protected $stepTime = 30;

    /**
     * @var Carbon
     */
    protected $startDate;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var bool
     */
    protected $startOnSunday = false;

    /**
     * @param string $key
     * @return static
     */
    public static function create($key)
    {
        $instance = new static;
        $instance->key = $key;

        $instance->label = "";

        return $instance;
    }

    public function type()
    {
        return "date";
    }

    /**
     * @param boolean $hasDate
     * @return SharpDateFormFieldConfig
     */
    public function setHasDate($hasDate)
    {
        $this->hasDate = $hasDate;

        return $this;
    }

    /**
     * @param boolean $hasTime
     * @return SharpDateFormFieldConfig
     */
    public function setHasTime($hasTime)
    {
        $this->hasTime = $hasTime;

        return $this;
    }

    /**
     * @param Carbon $minDate
     * @return SharpDateFormFieldConfig
     */
    public function setMinDate($minDate)
    {
        $this->minDate = $minDate;

        return $this;
    }

    /**
     * @param Carbon $maxDate
     * @return SharpDateFormFieldConfig
     */
    public function setMaxDate($maxDate)
    {
        $this->maxDate = $maxDate;

        return $this;
    }

    /**
     * @param Carbon $minTime
     * @return SharpDateFormFieldConfig
     */
    public function setMinTime($minTime)
    {
        $this->minTime = $minTime;

        return $this;
    }

    /**
     * @param Carbon $maxTime
     * @return SharpDateFormFieldConfig
     */
    public function setMaxTime($maxTime)
    {
        $this->maxTime = $maxTime;

        return $this;
    }

    /**
     * @param Carbon $startDate
     * @return SharpDateFormFieldConfig
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @param string $format
     * @return SharpDateFormFieldConfig
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @param boolean $startOnSunday
     * @return SharpDateFormFieldConfig
     */
    public function setStartOnSunday($startOnSunday)
    {
        $this->startOnSunday = $startOnSunday;

        return $this;
    }

    /**
     * @return boolean
     */
    public function hasDate()
    {
        return $this->hasDate;
    }

    /**
     * @return boolean
     */
    public function hasTime()
    {
        return $this->hasTime;
    }

    /**
     * @return Carbon
     */
    public function minDate()
    {
        return $this->minDate;
    }

    /**
     * @return Carbon
     */
    public function maxDate()
    {
        return $this->maxDate;
    }

    /**
     * @return Carbon
     */
    public function minTime()
    {
        return $this->minTime;
    }

    /**
     * @return Carbon
     */
    public function maxTime()
    {
        return $this->maxTime;
    }

    /**
     * @return Carbon
     */
    public function startDate()
    {
        return $this->startDate;
    }

    /**
     * @return string
     */
    public function format()
    {
        return $this->format;
    }

    /**
     * @return boolean
     */
    public function startOnSunday()
    {
        return $this->startOnSunday;
    }

    /**
     * @param int $stepTime
     * @return SharpDateFormFieldConfig
     */
    public function setStepTime($stepTime)
    {
        $this->stepTime = $stepTime;

        return $this;
    }

    /**
     * @return int
     */
    public function stepTime()
    {
        return $this->stepTime;
    }
}