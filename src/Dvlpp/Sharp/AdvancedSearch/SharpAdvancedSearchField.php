<?php namespace Dvlpp\Sharp\AdvancedSearch;

use Dvlpp\Sharp\AdvancedSearch\Fields\AdvancedSearchChooseField;
use Dvlpp\Sharp\AdvancedSearch\Fields\AdvancedSearchPivotTagsField;
use Dvlpp\Sharp\AdvancedSearch\Fields\AdvancedSearchTextField;
use Dvlpp\Sharp\Config\Entities\SharpEntityAdvancedSearchField;
use Form;

class SharpAdvancedSearchField {

    public function make($key, SharpEntityAdvancedSearchField $field)
    {
        $key = "adv_".$key;
        return $this->createField($key, $field);
    }

    protected function createField($key, SharpEntityAdvancedSearchField $field)
    {
        $attributes = $field->attributes ?: [];
        $attributes["autocomplete"] = "off";
        $this->addClass("form-control", $attributes);

        switch($field->type)
        {
            case 'text':
                $field = new AdvancedSearchTextField($key, $field, $attributes);
                return $field->make();

            case 'choose':
                $field = new AdvancedSearchChooseField($key, $field, $attributes);
                return $field->make();

//            case 'check':
//                $field = new CheckField($key, $field, $attributes);
//                return $field->make();

//            case 'ref':
//                $field = new RefField($key, $field, $attributes);
//                return $field->make();

            case 'pivot':
                $field = new AdvancedSearchPivotTagsField($key, $field, $attributes);
                return $field->make();

//            case 'date':
//                $field = new DateField($key, $field, $attributes);
//                return $field->make();


        }
        return null;
    }

    private function addClass($className, &$attributes)
    {
        $attributes["class"] = $className . (array_key_exists("class", $attributes) ? " ".$attributes["class"] : "");
    }

} 