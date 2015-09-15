<?php namespace Dvlpp\Sharp\Form\Fields;

/**
 * A markdown textarea input element, JS-built with lepture/editor.
 *
 * Class MarkdownField
 * @package Dvlpp\Sharp\Form\Fields
 */
class MarkdownField extends AbstractSharpField
{

    /**
     * The actual HTML creation of the field.
     *
     * @return mixed
     */
    function make()
    {
        if ($this->field->toolbar) {
            $this->addData("toolbar", $this->field->toolbar);
        }

        if ($this->field->height) {
            $this->addData("height", $this->field->height);
        }

        $this->addClass("sharp-markdown", true);

        // No need to populate the field, since we use a regular Form::textarea and a Form::model
        return $this->formBuilder()->textarea($this->fieldName, $this->fieldValue, $this->attributes);
    }


}