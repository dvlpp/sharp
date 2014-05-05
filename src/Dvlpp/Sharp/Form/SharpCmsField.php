<?php namespace Dvlpp\Sharp\Form;


use Dvlpp\Sharp\Config\Entities\SharpEntityFormField;
use Dvlpp\Sharp\Form\Fields\DateField;
use Dvlpp\Sharp\Form\Fields\FileField;
use Dvlpp\Sharp\Form\Fields\ListField;
use Dvlpp\Sharp\Form\Fields\MarkdownField;
use Dvlpp\Sharp\Form\Fields\RefField;
use Form;

class SharpCmsField {

    /**
     * Make the form field
     *
     * @param $key
     * @param \Dvlpp\Sharp\Config\Entities\SharpEntityFormField $field
     * @param $instance : the Model object valuated
     * @param null $listKey : the key of the list field if the current field is part of a list item
     * @return mixed
     */
    public function make($key, SharpEntityFormField $field, $instance, $listKey=null)
    {
        $label = $field->label ? $this->createLabel($key, $field->label) : '';
        $field = $this->createField($key, $field, $instance, $listKey);

        return $label.$field;
    }

    /**
     * Create the form field
     *
     * @param $key
     * @param \Dvlpp\Sharp\Config\Entities\SharpEntityFormField $field
     * @param $instance : the Model object valuated
     * @param $listKey
     * @return null|string
     */
    protected function createField($key, SharpEntityFormField $field, $instance, $listKey)
    {
        $attributes = $field->attributes ?: [];
        $attributes["autocomplete"] = "off";
        $this->addClass("form-control", $attributes);

        $fieldName = $listKey ? $listKey."[".($instance?$instance->id:"--N--")."][".$key."]" : $key;
        $fieldValue = null;
        if($listKey)
        {
            $fieldValue = $instance ? $instance->$key : null;
        }

        switch($field->type)
        {
            // First handle "regular" fields. No need to worry about $instance :
            // fields are auto-populated by Laravel, because we use a Form::model()
            case 'password': return Form::password($fieldName, $attributes);
            case 'text': return Form::text($fieldName, $fieldValue, $attributes);
            case 'textarea': return Form::textarea($fieldName, $fieldValue, $attributes);

            // Then special Sharp fields :
            case 'markdown':
                $field = new MarkdownField($key, $listKey, $field, $attributes, $instance);
                return $field->make();

            case 'file':
                $field = new FileField($key, $listKey, $field, $attributes, $instance);
                return $field->make();

            case 'list':
                $field = new ListField($key, $listKey, $field, $attributes, $instance);
                return $field->make();

            case 'ref':
                $field = new RefField($key, $listKey, $field, $attributes, $instance);
                return $field->make();

            case 'date':
                $field = new DateField($key, $listKey, $field, $attributes, $instance);
                return $field->make();

        }
        return null;
    }

    /**
     * Handle of creation of the label
     *
     * @param string $key
     * @param string $name
     */
    protected function createLabel($key, $name)
    {
        return Form::label($key, $name, ['class' => 'control-label']);
    }

    private function addClass($className, &$attributes)
    {
        $attributes["class"] = $className . (array_key_exists("class", $attributes) ? " ".$attributes["class"] : "");
    }

}