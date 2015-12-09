<?php

namespace Dvlpp\Sharp\Config\Utils;

/**
 * Implemented by form templates, responsible for displaying form fields.
 * Such as entity form (SharpFormTemplateColumnConfig)
 * or command form (SharpCommandFormTemplateConfig)
 *
 * Interface SharpFormTemplate
 * @package Dvlpp\Sharp\Config
 */
interface SharpFormTemplate
{
    /**
     * @param string $name
     */
    function addField($name);

    /**
     * @param string $name
     * @param array $fieldNames
     */
    function addFieldset($name, $fieldNames);

    /**
     * @param int $mode 1 for update, 2 for creation, 3 for both
     * @return array
     */
    public function fields($mode);
}