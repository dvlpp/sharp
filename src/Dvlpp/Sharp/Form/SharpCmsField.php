<?php namespace Dvlpp\Sharp\Form;

use Dvlpp\Sharp\Config\Entities\SharpEntityFormField;
use Dvlpp\Sharp\Form\Fields\CheckField;
use Dvlpp\Sharp\Form\Fields\ChooseField;
use Dvlpp\Sharp\Form\Fields\DateField;
use Dvlpp\Sharp\Form\Fields\EmbedField;
use Dvlpp\Sharp\Form\Fields\EmbedListField;
use Dvlpp\Sharp\Form\Fields\FileField;
use Dvlpp\Sharp\Form\Fields\HiddenField;
use Dvlpp\Sharp\Form\Fields\JavascriptCode;
use Dvlpp\Sharp\Form\Fields\LabelField;
use Dvlpp\Sharp\Form\Fields\ListField;
use Dvlpp\Sharp\Form\Fields\MarkdownField;
use Dvlpp\Sharp\Form\Fields\PasswordField;
use Dvlpp\Sharp\Form\Fields\PivotTagsField;
use Dvlpp\Sharp\Form\Fields\RefField;
use Dvlpp\Sharp\Form\Fields\RefSublistItemField;
use Dvlpp\Sharp\Form\Fields\TextareaField;
use Dvlpp\Sharp\Form\Fields\TextField;
use Form;

/**
 * Class SharpCmsField
 * @package Dvlpp\Sharp\Form
 */
class SharpCmsField
{

    /**
     * Make the form field
     *
     * @param $key
     * @param SharpEntityFormField $field
     * @param Object $instance : the Model object valuated
     * @param string|null $listKey : the key of the list field if the current field is part of a list item
     * @return mixed
     */
    public function make($key, $field, $instance, $listKey = null)
    {
        $label = $field->label ? $this->createLabel($key, $field->label) : '';
        $field = $this->createField($key, $field, $instance, $listKey);

        return $label . $field;
    }

    /**
     * Create the form field
     *
     * @param $key
     * @param SharpEntityFormField $field
     * @param Object $instance : the Model object valuated
     * @param string $listKey
     * @return null|string
     */
    protected function createField($key, $field, $instance, $listKey)
    {
        $attributes = $field->attributes ?: [];
        $attributes["autocomplete"] = "off";
        $this->addClass("form-control", $attributes);

        switch ($field->type) {
            case 'text':
                $formField = new TextField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();

            case 'password':
                $formField = new PasswordField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();

            case 'textarea':
                $formField = new TextareaField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();

            case 'choose':
            case 'select':
                $formField = new ChooseField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();

            case 'check':
                $formField = new CheckField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();

            case 'markdown':
                $formField = new MarkdownField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();

            case 'file':
                $formField = new FileField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();

            case 'list':
                $formField = new ListField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();

            case 'ref':
                $formField = new RefField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();

            case 'refSublistItem':
                $formField = new RefSublistItemField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();

            case 'pivot':
                $formField = new PivotTagsField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();

            case 'date':
                $formField = new DateField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();

            case 'hidden':
                $formField = new HiddenField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();

            case 'label':
                $formField = new LabelField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();

            case 'javascript':
                return (new JavascriptCode($field))->make();

            case 'embed':
                $formField = new EmbedField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();

            case 'embed_list':
                $formField = new EmbedListField($key, $listKey, $field, $attributes, $instance);

                return $formField->make();
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

    /**
     * Add a style class name to the field.
     *
     * @param $className
     * @param $attributes
     */
    private function addClass($className, &$attributes)
    {
        $attributes["class"] = $className . (array_key_exists("class", $attributes) ? " " . $attributes["class"] : "");
    }

}