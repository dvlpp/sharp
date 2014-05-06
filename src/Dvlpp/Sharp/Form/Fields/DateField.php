<?php namespace Dvlpp\Sharp\Form\Fields;

use Form;

class DateField extends AbstractSharpField {

    private static $availableOptions = [
        "has_date", "has_time", "step_time", "min_date", "max_date", "min_time", "max_time",
        "start_date", "format", "start_on_sunday"
    ];

    function make()
    {
        // Set options (parameters)
        foreach (self::$availableOptions as $opt) {
            if($this->field->$opt) $this->addData($opt, $this->field->$opt);
        }

        $format = $this->field->format;
        if(!$format)
        {
            $format = "";
            $format .= $this->field->has_date!==false ? trans('sharp::format.date_inputFormat') : "";
            $format .= $this->field->has_time ? ((strlen($format)?" ":"") . trans('sharp::format.time_inputFormat')) : "";

            $this->addData("format", $format);
        }

        $this->addData("lang", \Lang::locale());

        // Valuate field (date formatting according to declared format)
        $fieldValue = null;
        if($this->fieldValue)
        {
            $d = strtotime($this->fieldValue);
            if($d)
            {
                $fieldValue = date($format, $d);
            }
        }

        if(!$this->instance && $this->isListItem)
        {
            // No instance and part of a list item : this field is meant to be in the template item.
            // In this case, we don't set the "sharp-date" class which will trigger the JS code for
            // the date component creation
            $this->addClass("sharp-date-template");
        }
        else
        {
            // Regular case
            $this->addClass("sharp-date");
        }

        // Auto-populate the real sent field
        $str = Form::hidden($this->fieldName, $this->fieldValue, ["class"=>"sharp-date-timestamp", "autocomplete"=>"off"]);

        // And populate with formatted date the visible input field
        $str .= Form::text("__date__".$this->fieldName, $fieldValue, $this->attributes);

        return $str;
    }


}