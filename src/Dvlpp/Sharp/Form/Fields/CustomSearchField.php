<?php

namespace Dvlpp\Sharp\Form\Fields;

use Dvlpp\Sharp\Config\SharpCmsConfig;
use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Repositories\SharpHasCustomSearch;
use Form;

class CustomSearchField extends AbstractSharpField
{
    /**
     * The actual HTML creation of the field.
     * @return string
     * @throws MandatoryClassNotFoundException
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryEntityAttributeNotFoundException
     */
    function make()
    {
        $this->_checkMandatoryAttributes(['listitem_template', 'result_template', 'id_attribute']);
        $this->addData("template", $this->field->listitem_template);
        $this->addData("idattr", $this->field->id_attribute);

        if ($this->field->min_char) {
            $this->addData("minchar", $this->field->min_char);
        }

        $modalTitle = $this->field->modal_title ?: trans("sharp::ui.form_customSearchField_modalTitle");

        $this->addData("remote", route("cms.customSearchField", [
            $this->field->getCategoryKey(),
            $this->field->getEntityKey(),
            $this->fieldName
        ]));

        if (!$this->instance && $this->isListItem) {
            // No data and part of a list item: this field is meant to be in the template item.
            // In this case, we don't set the "sharp-file" class which will trigger the JS code for
            // the file upload component creation
            $this->addClass('sharp-customSearch-template');

        } else {
            $this->addClass('sharp-customSearch');
        }

        // Render the valuated view
        $valuated = false;
        $strValuatedView = "";

        if($this->fieldValue || $this->isRepopulated()) {
            // Instantiate entity repository
            $entity = SharpCmsConfig::findEntity($this->field->getCategoryKey(), $this->field->getEntityKey());
            $repo = app($entity->repository);

            if(!$repo instanceof SharpHasCustomSearch) {
                throw new MandatoryClassNotFoundException("Repository [{$entity->repository}] must implement the "
                    .'Dvlpp\Sharp\Repositories\SharpHasCustomSearch'
                    ." interface");
            }

            $value = $this->fieldValue ?: $this->getOldValue();

            $result = $repo->getCustomSearchResult($value);
            $strValuatedView = $this->wrapIntoPanel(view($this->field->result_template, $result)->render());
            $valuated = true;
        }

        $strTemplateView = $this->wrapIntoPanel(view($this->field->result_template)->render(), true);

        // Render the result modal (where results will be displayed)
        $strModal = view('sharp::cms.partials.fields.customsearchfield_modal', [
            "field" => $this->fieldName,
            "title" => $modalTitle
        ])->render();

        return '<div class="search '.($valuated?"hidden":"").'">' . Form::text("__customSearchBox__".$this->fieldName, "", $this->attributes) . '</div>'
                . Form::hidden($this->fieldName, $this->fieldValue, ["autocomplete" => "off"])
                . $strValuatedView
                . $strTemplateView
                . $strModal;
    }

    /**
     * Method called by the controller to perform the search (delegation to the repo)
     * and render the results, which will be sent back in JSON.
     *
     * @param $fieldName
     * @param SharpHasCustomSearch $repository
     * @param $request
     * @return array|null
     */
    public static function renderCustomSearch($fieldName, SharpHasCustomSearch $repository, $request)
    {
        try {
            // Call repo method
            $results = $repository->performCustomSearch($fieldName, $request->get("q"));

            $renderedResults = [];
            foreach ($results as $result) {
                $result = (array)$result;

                $renderedResults[] = [
                    "__id" => $result[$request->get("idattr")],
                    "html" => view($request->get("template"), $result)->render(),
                    "object" => $result
                ];
            }

            return $renderedResults;

        } catch(\Exception $e) {
            return null;
        }
    }

    private function wrapIntoPanel($view, $isTemplate=false)
    {
        return '<div class="panel panel-default '.($isTemplate?'panel-template':'panel-valuated').'"><div class="panel-body">'
            .$view
            .'<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
            .'</div></div>';
    }

}