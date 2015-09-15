<?php namespace Dvlpp\Sharp\Form;

use Collective\Html\FormBuilder;
use Dvlpp\Sharp\Config\Entities\SharpEntityFormField;
use Dvlpp\Sharp\Form\Fields\JavascriptCode;

/**
 * Class SharpCmsField
 * @package Dvlpp\Sharp\Form
 */
class SharpCmsField
{
    /**
     * @var FormBuilder
     */
    private $formBuilder;

    /**
     * SharpCmsField constructor.
     * @param FormBuilder $formBuilder
     */
    public function __construct(FormBuilder $formBuilder)
    {
        $this->formBuilder = $formBuilder;
    }


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

        if($field->type == "javascript") {
            return (new JavascriptCode($field))->make();
        }

        $className = 'Dvlpp\Sharp\Form\Fields\\' . ucfirst($field->type) . 'Field';

        if(class_exists($className)) {
            return (new $className($key, $listKey, $field, $attributes, $instance))->make();
        }

        return null;
    }

    /**
     * Handle of creation of the label
     *
     * @param string $key
     * @param string $name
     * @return string
     */
    protected function createLabel($key, $name)
    {
        return $this->formBuilder->label($key, $name, ['class' => 'control-label']);
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