<?php namespace Dvlpp\Sharp\Form;


use Dvlpp\Sharp\Config\Entities\SharpEntityFormField;
use Dvlpp\Sharp\Form\Fields\CheckField;
use Dvlpp\Sharp\Form\Fields\ChooseField;
use Dvlpp\Sharp\Form\Fields\DateField;
use Dvlpp\Sharp\Form\Fields\FileField;
use Dvlpp\Sharp\Form\Fields\ListField;
use Dvlpp\Sharp\Form\Fields\MarkdownField;
use Dvlpp\Sharp\Form\Fields\PasswordField;
use Dvlpp\Sharp\Form\Fields\PivotTagsField;
use Dvlpp\Sharp\Form\Fields\RefField;
use Dvlpp\Sharp\Form\Fields\RefSublistItemField;
use Dvlpp\Sharp\Form\Fields\TextareaField;
use Dvlpp\Sharp\Form\Fields\TextField;
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

        switch($field->type)
        {
            case 'text':
                $field = new TextField($key, $listKey, $field, $attributes, $instance);
                return $field->make();

            case 'password':
                $field = new PasswordField($key, $listKey, $field, $attributes, $instance);
                return $field->make();

            case 'textarea':
                $field = new TextareaField($key, $listKey, $field, $attributes, $instance);
                return $field->make();

            case 'choose':
                $field = new ChooseField($key, $listKey, $field, $attributes, $instance);
                return $field->make();

            case 'check':
                $field = new CheckField($key, $listKey, $field, $attributes, $instance);
                return $field->make();

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

            case 'refSublistItem':
                $field = new RefSublistItemField($key, $listKey, $field, $attributes, $instance);
                return $field->make();

            case 'pivot':
                $field = new PivotTagsField($key, $listKey, $field, $attributes, $instance);
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