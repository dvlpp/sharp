<?php

namespace Dvlpp\Sharp\Form\Fields;

/**
 * A field upload input element
 *
 * Class FileField
 * @package Dvlpp\Sharp\Form\Fields
 */
class FileField extends AbstractSharpField
{

    /**
     * The actual HTML creation of the field.
     *
     * @return string
     */
    function make()
    {
        // Manage the thumbnail data attribute
        if ($this->field->thumbnail) {
            $this->addData('thumbnail', $this->field->thumbnail);
        }
        if ($this->field->file_filter) {
            $this->addData('file_filter', $this->field->file_filter);
        }
        if ($this->field->file_filter_alert) {
            $this->addData('file_filter_alert', $this->field->file_filter_alert);
        }
        if ($this->field->max_file_size) {
            $this->addData('max_file_size', $this->field->max_file_size);
        }

        $this->addData('browse_text', trans('sharp::ui.form_fileField_browseText'));

        $this->addClass('sharp-file', true);
        if($this->field->thumbnail) {
            $this->addClass('with-thumbnail');
        }

        // Gets the file possibly valuated
        $instanceFile = null;

        if ($this->fieldValue) {
            // File valued: have to grab the full file path

            if (is_string($this->fieldValue) && starts_with($this->fieldValue, ":DUPL:")) {
                // Duplication case: file path is in the value
                $instanceFile = substr($this->fieldValue, strlen(":DUPL:"));

            } else {
                // Populate from "normal" data (field): we get the file path from the model
                if ($this->relation) {
                    // Single relationship ~ case
                    $ownerInstance = $this->instance->{$this->relation};
                    $key = $this->relationKey;
                } else {
                    $ownerInstance = $this->instance;
                    $key = $this->key;
                }

                if (method_exists($ownerInstance, "getSharpFilePathFor")) {
                    $instanceFile = $ownerInstance->getSharpFilePathFor($key);

                } elseif (is_object($this->fieldValue) && method_exists($this->fieldValue, "getSharpFilePath")) {
                    // Optional second method: call getSharpField on the File object itself (if it's an object).
                    // Useful when files are stored in separate table
                    $instanceFile = $this->fieldValue->getSharpFilePath();
                }
            }

            if($instanceFile) {
                $this->addClass('valuated');
            }
        }

        $this->initJSValuatedValues($instanceFile);

        // And here we go
        $strField = '<div class="'
            . $this->attributes["class"]
            . '" ' . $this->getDataValues() . '>';

        // Valuation of the Form::hidden (value)
        $strField .= $this->formBuilder()->hidden($this->fieldName,
            $this->instance && isset($this->instance->__sharp_duplication) && $this->instance->__sharp_duplication
                ? ":DUPL:" . $instanceFile // Duplication case: we provide the full original file path
                : $this->fieldValue, // Regular case: value is the field value
            ["class" => "sharp-file-id", "autocomplete" => "off"]);

        return $strField . '</div>';
    }

    /**
     * @param $instanceFile
     */
    private function initJSValuatedValues($instanceFile)
    {
        if(!$instanceFile) return;

        if ($this->field->thumbnail) {
            // There's a thumbnail to display
            list($w, $h) = explode("x", $this->field->thumbnail);
            $this->addData('thumbnail', sharp_thumbnail($instanceFile, $w, $h));
        }

        // Manage label
        $this->addData('name', basename($instanceFile));

//        $this->addData('size', \Storage::disk(config("sharp.upload_storage_disk"))->size($relativeFilePath));

        // Add download link
        $this->addData('dl_link', route("download", $instanceFile));
    }

    private function getDataValues()
    {
        $data = [];

        foreach($this->attributes as $key => $value) {
            if(starts_with($key, "data-")) {
                $data[] = $key.'="'.$value.'"';
            }
        }

        return implode(" ", $data);
    }
}