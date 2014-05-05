<?php namespace Dvlpp\Sharp\Form\Fields;


use Form;

class MarkdownField extends AbstractSharpField {

    function make()
    {
        if($this->field->toolbar) $this->addData("toolbar", $this->field->toolbar);
        if($this->field->height) $this->addData("height", $this->field->height);

        if(!$this->instance && $this->isListItem)
        {
            // No instance and part of a list item : this field is meant to be in the template item.
            // In this case, we don't set the "sharp-markdown" class which will trigger the JS code for
            // the markdown component creation
            $this->addClass("sharp-markdown-template");
        }
        else
        {
            // Regular case
            $this->addClass("sharp-markdown");
        }

        // No need to populate the field, since we use a regular Form::textarea and a Form::model
        return (string) Form::textarea($this->fieldName, $this->fieldValue, $this->attributes);
    }


}