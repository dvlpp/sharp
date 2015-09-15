<?php namespace Dvlpp\Sharp\Form\Fields;

use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Exceptions\MandatoryMethodNotFoundException;

/**
 * A multiple tags input.
 *
 * Class PivotTagsField
 * @package Dvlpp\Sharp\Form\Fields
 */
class PivotField extends AbstractSharpField {

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

        if(class_exists($reflistRepoName) || interface_exists($reflistRepoName)) {
            $reflistRepo = app($reflistRepoName);

            if($this->field->addable !== null) {
                $this->addData("addable", $this->field->addable);
            }

            $this->addData("add_text", trans('sharp::ui.form_pivotTagsField_addText'));

            $this->addClass("sharp-tags", true);

            // Have to set multiple attribute in order to properly generate the field
            $this->attributes["multiple"] = "multiple";

            if(method_exists($reflistRepo, "formList")) {
                $values = $reflistRepo->formList($this->instance);

                $value = $this->getInitialValue();
                if($value) {
                    // Have to sort the $values to make sure that items in $value are
                    // displayed in the right order.
                    uksort($values, function ($a, $b) use($value) {
                        if(in_array($a, $value)) {
                            if(in_array($b, $value)) {
                                return array_search($a, $value) - array_search($b, $value);
                            }
                            return 1;
                        }

                        if(in_array($b, $value)) {
                            return -1;
                        }

                        return $b-$a;
                    });
                }

                // Field name has to be an array (books[] for example) to generate an array on data post
                return '<input type="hidden" name="'.$this->fieldName.'" value="">'
                    . $this->formBuilder()->select($this->fieldName . "[]", $values, $value, $this->attributes);
            }

            throw new MandatoryMethodNotFoundException("Method formList(askingInstance) not found in the [$reflistRepoName] class");
        }

        throw new MandatoryClassNotFoundException("Class [$reflistRepoName] not found");
    }

    /**
     * @return array
     */
    private function getInitialValue()
    {
        $value = [];

        if ($this->fieldValue) {
            foreach ($this->fieldValue as $val) {
                $value[] = $val->id;
            }
        }

        return $value;
    }
} 