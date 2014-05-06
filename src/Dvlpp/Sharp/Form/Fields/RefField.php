<?php namespace Dvlpp\Sharp\Form\Fields;


use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Exceptions\MandatoryMethodNotFoundException;
use Form;
use App;

class RefField extends AbstractSharpField {

    function make()
    {

        $this->_checkMandatoryAttributes(["repository"]);

        $reflistRepoName = $this->field->repository;
        if(class_exists($reflistRepoName) || interface_exists($reflistRepoName))
        {
            $reflistRepo = App::make($reflistRepoName);
            if(method_exists($reflistRepo, "formList"))
            {
                $values = $reflistRepo->formList();

                // No need to populate the field, since we use a regular Form::select and a Form::model
                return Form::select($this->fieldName, $values, $this->fieldValue, $this->attributes);
            }
            else
            {
                throw new MandatoryMethodNotFoundException("Method formList() not found in the $reflistRepoName class");
            }
        }
        else
        {
            throw new MandatoryClassNotFoundException("Class $reflistRepoName not found");
        }

    }


}