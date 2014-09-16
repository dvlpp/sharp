<?php namespace Dvlpp\Sharp\Form\Fields;

use Form;
use Lang;

/**
 * An ordered list of items each containing a single EmbedField
 *
 * Class EmbedListField
 * @package Dvlpp\Sharp\Form\Fields
 */
class EmbedListField extends ListField {

    /**
     * Create the item, which is a single EmbedField
     *
     * @param $item
     * @return string
     */
    protected function createItemField($item)
    {
        $embedField = new EmbedField("__embed_data", $this->key, $this->field, [], $item);

        return '<div class="col-md-12">'
            . '<div class="form-group sharp-field sharp-field-embed">'
            . $embedField->make()
            . '</div></div>';
    }

} 