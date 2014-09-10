<?php namespace Dvlpp\Sharp\AdvancedSearch\Fields;

use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Exceptions\MandatoryMethodNotFoundException;
use App;
use Form;

class AdvancedSearchPivotTagsField extends AdvancedSearchAbstractField {

    public function make()
    {
        $reflistRepoName = $this->field->repository;

        if(class_exists($reflistRepoName) || interface_exists($reflistRepoName))
        {
            $reflistRepo = App::make($reflistRepoName);

            $this->addClass("sharp-advancedsearch-tags");

            // Have to set multiple attribute in order to properly generate the field
            $this->attributes["multiple"] = "multiple";

            if(method_exists($reflistRepo, "formList"))
            {
                $values = $reflistRepo->formList(null);

                // Field name has to be an array (books[] for example) to generate an array on data post
                return Form::select($this->key . "[]", $values, $this->value, $this->attributes);
            }

            throw new MandatoryMethodNotFoundException("Method formList() not found in the [$reflistRepoName] class");
        }
        else
        {
            throw new MandatoryClassNotFoundException("Class [$reflistRepoName] not found");
        }
    }
}