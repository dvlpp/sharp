<?php

namespace Dvlpp\Sharp\Form\Fields;

use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Exceptions\MandatoryMethodNotFoundException;

/**
 * A reference picker field.
 *
 * Class RefField
 * @package Dvlpp\Sharp\Form\Fields
 */
class RefField extends AbstractSharpField
{

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

        $reflistRepoName = $this->field->repository();

        if (class_exists($reflistRepoName) || interface_exists($reflistRepoName)) {
            $reflistRepo = app($reflistRepoName);

            $this->addData("create", $this->field->creatable());

            $this->addClass("sharp-ref", true);

            if (method_exists($reflistRepo, "formList")) {
                $values = $reflistRepo->formList($this->instance);

                return $this->formBuilder()->select($this->fieldName, $values, $this->fieldValue, $this->attributes);
            }

            throw new MandatoryMethodNotFoundException("Method formList(askingInstance) not found in the [$reflistRepoName] class");
        }

        throw new MandatoryClassNotFoundException("Class [$reflistRepoName] not found");
    }


}