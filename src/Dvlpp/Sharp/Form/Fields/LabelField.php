<?php

namespace Dvlpp\Sharp\Form\Fields;

use Dvlpp\Sharp\Exceptions\MandatoryEntityAttributeNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Mustache_Engine;

/**
 * A simple display Label, with no posted value.
 *
 * Class LabelField
 * @package Dvlpp\Sharp\Form\Fields
 */
class LabelField extends AbstractSharpField {

    /**
     * The actual HTML creation of the field.
     *
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryEntityAttributeNotFoundException
     */
    function make()
    {
        if($this->field->format() === null) {
            throw new MandatoryEntityAttributeNotFoundException("LabelField : Mandatory attribute format can't be found");
        }

        // Valuate field with Mustache templating
        $baseEntity = $this->instance;
        if ($baseEntity instanceof Model) {
            // Eloquent Model case: in order to have properties to work with Mustache, we
            // have to cheat a little, adding a MustacheModelHelper Decorator to force
            // Mustache to take properties even if method exists (relation case)
            $baseEntity = new MustacheModelHelper($baseEntity);
        }
        $value = $this->_format($baseEntity, $this->field->format());

        $attributes = $this->field->attributes() || [];
        $attributes["class"] = "control-label";

        if($this->field->style()) {
            $attributes["style"] = $this->field->style();
        }

        $div = '<label';
        foreach($attributes as $attr => $val) {
            $div .= " $attr=\"$val\"";
        }
        $div .= ">$value</label>";

        $str = '<input type="hidden" name="' . $this->fieldName . '" value="' . $value . '">'
            . $div;

        return $str;
    }

    /**
     * @param $entity
     * @param $format
     * @return string
     */
    private function _format($entity, $format)
    {
        $m = new Mustache_Engine;
        return $m->render($format, $entity);
    }

}

/**
 * Eloquent Model decorator, which purpose is to invert Mustache
 * order of precedence between Methods and Properties
 * (source: https://github.com/bobthecow/mustache.php/issues/156)
 *
 * Class MustacheModelHelper
 * @package Dvlpp\Sharp\Form\Fields
 */
class MustacheModelHelper
{
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function __get($key)
    {
        if (method_exists($this->model, $key)) {
            // It's either a Relation method or a standard one
            $ret = $this->model->$key();
            if($ret instanceof Relation) {
                // It's a Relation: return the attribute in order
                // to make Eloquent relation work
                return $this->model->$key;
            }

            // Standard method
            return $this->model->$key();

        } elseif(isset($this->model->$key)) {
            return $this->model->$key;
        }

        return null;
    }

    public function __isset($key)
    {
        if (property_exists($this->model, $key) || method_exists($this->model, $key)) {
            return true;
        }

        return false;
    }
}