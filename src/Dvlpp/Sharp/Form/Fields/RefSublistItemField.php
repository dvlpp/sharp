<?php namespace Dvlpp\Sharp\Form\Fields;

use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Exceptions\MandatoryMethodNotFoundException;
use App;
use Form;

/**
 * This field stands for a reference to a list item of a RefField.
 * For instance: an object might have a reference toward
 * a particular row (list item) of a Command
 *
 * Class RefSublistItemField
 * @package Dvlpp\Sharp\Form\Fields
 */
class RefSublistItemField extends AbstractSharpField {

    /**
     * The actual HTML creation of the field.
     *
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryMethodNotFoundException
     * @return string
     */
    function make()
    {
        $this->_checkMandatoryAttributes(["repository","linked_ref_field","ref_list_id"]);

        $repoName = $this->field->repository;
        if(class_exists($repoName) || interface_exists($repoName))
        {
            $repo = App::make($repoName);

            if(!$this->instance && $this->isListItem)
            {
                // No instance and part of a list item: this field is meant to be in the template item.
                // In this case, we don't set the "sharp-refSublistItem" class which will trigger the JS code for
                // the selectize component creation
                $this->addClass("sharp-refSublistItem-template");
            }
            else
            {
                // Regular case
                $this->addClass("sharp-refSublistItem");
            }

            $this->addData("linked_ref_field", $this->field->linked_ref_field);

            if(method_exists($repo, "formListForSublist"))
            {
                $allValues = $repo->formListForSublist($this->field->ref_list_id, $this->instance);

                // First we create a "datastore" hidden select, with all values,
                // to allow JS code to search in
                $str = Form::select($this->fieldName . "_values", $allValues, null, ["class"=>"hidden"]);

                $value = $this->getOldValue() ?: $this->fieldValue;

                // We have to manually handle the initial value, in JS code,
                // because of linked selects
                $this->addData("initial_value", $value);

                $str .= Form::select($this->fieldName, [], $value, $this->attributes);

                return $str;
            }

            throw new MandatoryMethodNotFoundException("Method formListForSublist(refListId, askingInstance) not found in the [$repoName] class");
        }
        else
        {
            throw new MandatoryClassNotFoundException("Class [$repoName] not found");
        }

    }


}