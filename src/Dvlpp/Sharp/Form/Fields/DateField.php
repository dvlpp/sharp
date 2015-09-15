<?php namespace Dvlpp\Sharp\Form\Fields;

/**
 * A date/time input element, JS-built with http://xdsoft.net/jqplugins/datetimepicker/.
 *
 * Class DateField
 * @package Dvlpp\Sharp\Form\Fields
 */
class DateField extends AbstractSharpField {

    /**
     * @var array
     */
    private static $availableOptions = [
        "has_date", "has_time", "step_time", "min_date", "max_date", "min_time", "max_time",
        "start_date", "format", "start_on_sunday"
    ];

    /**
     * The actual HTML creation of the field.
     *
     * @return string
     */
    function make()
    {
        // Set options (parameters)
        foreach (self::$availableOptions as $opt) {
            if($this->field->$opt) $this->addData($opt, $this->field->$opt);
        }

        $format = $this->field->format;
        if(!$format) {
            $format = "";
            $format .= $this->field->has_date!==false ? trans('sharp::format.date_inputFormat') : "";
            $format .= $this->field->has_time ? ((strlen($format)?" ":"") . trans('sharp::format.time_inputFormat')) : "";

            $this->addData("format", $format);
        }

        $this->addData("lang", \Lang::locale());

        // Valuate field (date formatting according to declared format)
        $fieldValue = null;
        if($this->fieldValue && ($d = strtotime($this->fieldValue))) {
            $fieldValue = date($format, $d);
        }

        $this->addClass("sharp-date", true);

        // Auto-populate the real sent field
        $str = $this->formBuilder()->hidden($this->fieldName, $this->fieldValue, ["class"=>"sharp-date-timestamp", "autocomplete"=>"off"]);

        // And populate with formatted date the visible input field
        $str .= $this->formBuilder()->text("__date__".$this->fieldName, $fieldValue, $this->attributes);

        return $str;
    }

}