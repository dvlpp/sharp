<?php

namespace Dvlpp\Sharp\Form\Fields;

use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Repositories\SharpHasCustomSearch;

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
        $this->_checkMandatoryAttributes(['listItemTemplate', 'resultTemplate', 'idAttribute']);
        $this->addData("template", $this->field->listItemTemplate());
        $this->addData("idattr", $this->field->idAttribute());

        if ($this->field->searchMinChar()) {
            $this->addData("minchar", $this->field->searchMinChar());
        }

        $modalTitle = $this->field->modalTitle() ?: trans("sharp::ui.form_customSearchField_modalTitle");

        $this->addData("remote", route("sharp.cms.customSearchField", [
            $this->field->entity()->categoryKey(),
            $this->field->entity()->key(),
            $this->fieldName
        ]));

        $this->addClass("sharp-customSearch", true);

        // Render the valuated view
        $valuated = false;
        $strValuatedView = "";

        if($this->fieldValue) {
            // Instantiate entity repository
            $entity = sharp_entity($this->field->entity()->categoryKey(), $this->field->entity()->key());
            $repo = app($entity->repository());

            if(!$repo instanceof SharpHasCustomSearch) {
                throw new MandatoryClassNotFoundException("Repository [{$entity->repository()}] must implement the "
                    . SharpHasCustomSearch::class . " interface");
            }

            $value = $this->fieldValue;

            $result = $repo->getCustomSearchResult($value);
            $strValuatedView = $this->wrapIntoPanel(view($this->field->resultTemplate(), $result)->render());
            $valuated = true;
        }

        $strTemplateView = $this->wrapIntoPanel(view($this->field->resultTemplate())->render(), true);

        // Render the result modal (where results will be displayed)
        $strModal = view('sharp::cms.partials.fields.customsearchfield_modal', [
            "field" => $this->fieldName,
            "title" => $modalTitle
        ])->render();

        return '<div class="search '.($valuated?"hidden":"").'">'
                . $this->formBuilder()->text("__customSearchBox__".$this->fieldName, "", $this->attributes)
                . '</div>'
                . $this->formBuilder()->hidden($this->fieldName, $this->fieldValue, ["autocomplete" => "off"])
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
    }

    private function wrapIntoPanel($view, $isTemplate=false)
    {
        return '<div class="panel panel-default '.($isTemplate?'panel-template':'panel-valuated').'"><div class="panel-body">'
            .$view
            .'<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
            .'</div></div>';
    }

}