<?php namespace Dvlpp\Sharp\Form\Fields;

use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Exceptions\MandatoryMethodNotFoundException;
use Form;
use App;
use Input;

/**
 * A reference picker field.
 *
 * Class RefField
 * @package Dvlpp\Sharp\Form\Fields
 */
class RefField extends AbstractSharpField {

    /**
     * The actual HTML creation of the field.
     *
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryMethodNotFoundException
     */
    function make()
    {

        $this->_checkMandatoryAttributes(["repository"]);

        $reflistRepoName = $this->field->repository;
        if(class_exists($reflistRepoName) || interface_exists($reflistRepoName))
        {
            $reflistRepo = app($reflistRepoName);

            /*$ui = $this->field->ui;
            if(!$ui)
            {
                $ui = "list";
            }
            elseif(!in_array($ui, ["list", "autocomplete"]))
            {
                throw new InvalidArgumentException("Attribute ui of ref field has to in 'list' or 'autocomplete'");
            }

            $this->addData("ui", $ui);*/

            $create = $this->field->create;
            if($create !== null)
            {
                $this->addData("create", $create);
            }

            if(!$this->instance && $this->isListItem)
            {
                // No instance and part of a list item : this field is meant to be in the template item.
                // In this case, we don't set the "sharp-ref" class which will trigger the JS code for
                // the selectize component creation
                $this->addClass("sharp-ref-template");
            }
            else
            {
                // Regular case
                $this->addClass("sharp-ref");
            }

            if(method_exists($reflistRepo, "formList"))
            {
                $values = $reflistRepo->formList($this->instance);

                // Initial value *could be* tricky...
                $value = $this->fieldValue;
                if($this->getOldValue() && !is_numeric($this->getOldValue()))
                {
                    // Repopulate after validation error for a ref which was created by user before.
                    // Have to tell selectize.js to add this non existent option
                    $value = $this->getOldValue();
                    $this->addData("to_add", $value);
                }

                return Form::select($this->fieldName, $values, $value, $this->attributes);
            }

            throw new MandatoryMethodNotFoundException("Method formList(askingInstance) not found in the [$reflistRepoName] class");
        }
        else
        {
            throw new MandatoryClassNotFoundException("Class [$reflistRepoName] not found");
        }

    }


}