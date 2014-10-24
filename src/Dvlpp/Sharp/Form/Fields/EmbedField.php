<?php namespace Dvlpp\Sharp\Form\Fields;

use Dvlpp\Sharp\Config\SharpCmsConfig;
use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use App;
use Lang;
use Input;
use Form;

/**
 * Class EmbedField
 * @package Dvlpp\Sharp\Form\Fields
 */
class EmbedField extends AbstractSharpField {

    /**
     * The actual HTML creation of the field.
     *
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException
     * @return mixed
     */
    function make()
    {
        $this->_checkMandatoryAttributes(["entity_category", "entity", "renderer"]);

        $strField = "";

        $initialVal = "";
        if($this->fieldValue && $this->getOldValue() === null)
        {
            // First time we display the embed field. We have to valuate its __embed_data field,
            // in order to post the current value of the embedded object
            $embeddedEntityConfig = SharpCmsConfig::findEntity($this->field->entity_category, $this->field->entity);
            $std = [];
            foreach($embeddedEntityConfig->data["form_fields"] as $fieldKey=>$configField)
            {
                if($this->instance && isset($this->instance->__sharp_duplication)
                    && $this->instance->__sharp_duplication
                    && $this->fieldValue->$fieldKey
                    && $configField["type"] == "file")
                {
                    // Hum... This is a special case. Indeed.
                    // Duplication case + valuated file field: have to put the correct data format
                    // for this, and it's :DUPL: + the file path.
                    // @see FileField
                    $std[$fieldKey] = ":DUPL:" . $this->instance->getSharpFilePathFor($fieldKey);
                }
                else
                {
                    $std[$fieldKey] = $this->fieldValue->$fieldKey;
                }
            }

            $std["__sharp_duplication"] = $this->instance->__sharp_duplication;

            $initialVal = sharp_encode_embedded_entity_data($std);
        }

        $strHiddenValue = Form::hidden($this->fieldName, $initialVal);

        $embeddedInstance = $this->getEmbeddedInstance();

        $fieldState = $embeddedInstance ? "updatable" : "creatable";

        if($embeddedInstance)
        {
            // Embed is valued: we have to "render" the field
            $renderer = App::make($this->field->renderer);

            if(!$renderer instanceof SharpEmbedFieldRenderer)
            {
                throw new MandatoryClassNotFoundException("Class [".$this->field->renderer
                    ."] must implements Dvlpp\\Sharp\\Form\\Fields\\SharpEmbedFieldRenderer");
            }

            $strField .= $renderer->render($embeddedInstance, $this->instance);
        }

        // ... and wrap it all in a bootstrap panel
        $strField = $strHiddenValue
            . '<div class="panel panel-default panel-embed '.$fieldState
            . '"><div class="panel-body panel-embed-body">'
            . $strField
            . '</div><div class="panel-footer">'
            . $this->buildEmbedButtons($embeddedInstance?$embeddedInstance->id:0)
            . '</div></div>';

        return $strField;

    }

    /**
     * Retrieve or build the instance of the embed field, taking count of repopulation
     *
     * @return null|string
     */
    public function getEmbeddedInstance()
    {
        $embeddedInstance = $this->fieldValue;

        $oldInputData = $this->getOldValue();

        if ($oldInputData !== null)
        {
            // Repopulation: in the EmbedField case, this means either that the master instance was invalid
            // or simply that the embedded instance was updated (all the embedded instance data is stored in the master
            // form, in order to perform the update all at once).
            // We don't need to update fields value, but we do need to create the new embed Object for the renderer

            $embeddedInstance = null;

            if($oldInputData && $oldInputData != "__DELETE__")
            {
                $embeddedEntityConfig = SharpCmsConfig::findEntity($this->field->entity_category, $this->field->entity);
                $embeddedEntityRepo = App::make($embeddedEntityConfig->repository);
                $embeddedInstance = $embeddedEntityRepo->newInstance();

                $embeddedInstanceData = sharp_decode_embedded_entity_data($oldInputData);

                if(is_array($embeddedInstanceData))
                {
                    foreach ($embeddedInstanceData as $attr => $value)
                    {
                        $embeddedInstance->$attr = $value;
                    }
                }
            }
        }

        return $embeddedInstance;
    }

    /**
     * @param $embedId
     * @return string
     */
    private function buildEmbedButtons($embedId)
    {
        $fieldName = $this->fieldName;
        if(strpos($fieldName, "[") !== false)
        {
            // Embed List case
            $fieldName = str_replace("]", "", str_replace("[", ".", $fieldName));
        }

        $strDelete = '<a class="sharp-embed-delete btn btn-sm" data-fieldname="'
            . $fieldName
            . '"><i class="fa fa-times"></i> '
            . Lang::get('sharp::ui.form_embedField_deleteBtn')
            . '</a>';

        $strUpdate = '<a class="sharp-embed-update btn btn-sm" href="'
            . route('cms.embedded.edit', [
                $this->field->getCategoryKey(),
                $this->field->getEntityKey(),
                $fieldName,
                $this->field->entity_category,
                $this->field->entity,
                $embedId
            ])
            . '"><i class="fa fa-pencil"></i> '
            . Lang::get('sharp::ui.form_embedField_editBtn')
            . '</a>';

        $strCreate = '<a class="sharp-embed-update sharp-embed-create btn" href="'
            . route('cms.embedded.create', [
                $this->field->getCategoryKey(),
                $this->field->getEntityKey(),
                $fieldName,
                $this->field->entity_category,
                $this->field->entity])
            . '"><i class="fa fa-plus"></i> '
            . Lang::get('sharp::ui.form_embedField_createBtn')
            . '</a>';

        return $strDelete . $strUpdate . $strCreate;
    }


    /**
     * Return the old value of the field, managing the embedlist special case.
     *
     * @return mixed
     */
    protected function getOldValue()
    {
        if(strpos($this->fieldName, "[") !== false)
        {
            // List item case
            $fieldNameList = str_replace("]", "", str_replace("[", ".", $this->fieldName));
            return Input::old($fieldNameList);
        }

        return Input::old($this->fieldName);
    }

}