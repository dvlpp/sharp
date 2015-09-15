<?php namespace Dvlpp\Sharp\Form\Fields;

use Form;

/**
 * A field upload input element, using https://github.com/blueimp.
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
        $strAttr = "";
        if ($this->field->thumbnail) {
            $strAttr = 'data-thumbnail="' . e($this->field->thumbnail) . '"';
        }
        if ($this->field->file_filter) {
            $strAttr .= ' data-file_filter="' . e($this->field->file_filter) . '"';
        }
        if ($this->field->file_filter_alert) {
            $strAttr .= ' data-file_filter_alert="' . e($this->field->file_filter_alert) . '"';
        }

        $strAttr .= ' data-browse_text="' . trans('sharp::ui.form_fileField_browseText') . '"';

        // Gets the file possibly valuated
        $instanceFile = null;
        $className = "sharp-file";

//        // Field name
//        if ($this->isListItem) {
//            // List item case: have to format the fieldName from [list][item][field] to list.item.field
//            $fieldName = str_replace("[", ".", $this->fieldName);
//            $fieldName = str_replace("]", "", $fieldName);
//        } else {
//            $fieldName = $this->fieldName;
//        }

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

            $className .= ($instanceFile ? ' valuated' : '');

        } elseif (!$this->instance && $this->isListItem) {
            // No data and part of a list item: this field is meant to be in the template item.
            // In this case, we don't set the "sharp-file" class which will trigger the JS code for
            // the file upload component creation
            $className = 'sharp-file-template';
        }

        $strField = '<div class="' . $className . ($this->field->thumbnail ? ' with-thumbnail' : '') . '" ' . $strAttr . '>';

        // Here we have to manually manage the field valuation (and it's a pain)
        if ($instanceFile) {
            // This file is populated

            if ($this->field->thumbnail) {
                // There's a thumbnail to display
                list($w, $h) = explode("x", $this->field->thumbnail);
                $strField .= '<div class="sharp-file-image"><img class="sharp-file-thumbnail" src="'
                    . sharp_thumbnail($instanceFile, $w, $h)
                    . '"></div>';
            }

            // Manage label
            $strField .= '<div class="sharp-file-label">'
                . '<div class="type"><i class="fa fa-file-o"></i><span>' . (file_exists($instanceFile) ? pathinfo($instanceFile,
                    PATHINFO_EXTENSION) : "") . '</span></div>'
                . '<span class="mime">(' . (file_exists($instanceFile) ? mime_content_type($instanceFile) : "") . ')</span>'
                . '<span class="size">' . (file_exists($instanceFile) ? $this->humanFileSize(filesize($instanceFile)) : "") . '</span>';

            // Add download link
            $fileShortPath = substr($instanceFile, strlen(config("sharp.upload_storage_base_path")) + 1);

            $strField .= '<a href="'
                . route("download", [$fileShortPath])
                . '" class="dl" target="_blank"><i class="fa fa-download"></i> '
                . trans('sharp::ui.form_fileField_dlBtn')
                . '</a>';

            if ($this->field->crop && !$this->isListItem) {
                $strField .= '<a class="sharp-file-crop" href="'
                    . sharp_thumbnail($instanceFile, 410, 410)
                    . '" data-ratio="'
                    . ($this->field->crop_ratio ?: '')
                    . '"><i class="fa fa-crop"></i> ' . trans('sharp::ui.form_fileField_cropBtn') . '</a>';
            }

            $strField .= '</div>';
        }

        // Valuation of the Form::hidden (value)
        $strField .= $this->formBuilder()->hidden($this->fieldName,
            $this->instance && isset($this->instance->__sharp_duplication) && $this->instance->__sharp_duplication
                ? ":DUPL:" . $instanceFile // Duplication case: we provide the full original file path
                : $this->fieldValue, // Regular case: value is the field value
            ["class" => "sharp-file-id", "autocomplete" => "off"]);

        if ($this->field->crop) {
            // ... and finally, crop

            // In single relation case (~), we use the relationKey for name only
            $fileName = $this->relation ? $this->relationKey : $this->fieldName;

            $strField .= $this->formBuilder()->hidden("__filecrop__$fileName",
                "",
                ["class" => "sharp-file-crop-values", "autocomplete" => "off"]);
        }

        return $strField . '</div>';
    }

    /**
     * @param $size
     * @return string
     */
    private function humanFileSize($size)
    {
        if ($size >= 1 << 30) {
            return number_format($size / (1 << 30), 2) . " " . trans('sharp::ui.form_filefield_filesize_GB');
        }
        if ($size >= 1 << 20) {
            return number_format($size / (1 << 20), 1) . " " . trans('sharp::ui.form_filefield_filesize_MB');
        }
        if ($size >= 1 << 10) {
            return number_format($size / (1 << 10), 0) . " " . trans('sharp::ui.form_filefield_filesize_KB');
        }

        return number_format($size) . " " . trans('sharp::ui.form_filefield_filesize_b');
    }
}