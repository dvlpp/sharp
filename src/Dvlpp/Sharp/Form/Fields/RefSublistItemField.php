<?php namespace Dvlpp\Sharp\Form\Fields;

use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Exceptions\MandatoryMethodNotFoundException;

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
        if(class_exists($repoName) || interface_exists($repoName)) {
            $repo = app($repoName);

            $this->addClass("sharp-refSublistItem", true);
            $this->addData("linked_ref_field", $this->field->linked_ref_field);

            if(method_exists($repo, "formListForSublist")) {
                $allValues = $repo->formListForSublist($this->field->ref_list_id, $this->instance);

                // We have to manually handle the initial value, in JS code,
                // because of linked selects
                $this->addData("initial_value", $this->fieldValue);

                // First we create a "datastore" hidden select, with all values,
                // to allow JS code to search in
                $str = $this->formBuilder()->select("values_" . $this->fieldName, $allValues, null, ["class"=>"hidden"]);

                // And then the select
                $str .= $this->formBuilder()->select($this->fieldName, [], $this->fieldValue, $this->attributes);

                return $str;
            }

            throw new MandatoryMethodNotFoundException("Method formListForSublist(refListId, askingInstance) not found in the [$repoName] class");
        }

        throw new MandatoryClassNotFoundException("Class [$repoName] not found");
    }

}