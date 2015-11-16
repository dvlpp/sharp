<?php

namespace Dvlpp\Sharp\Config\Utils;

use Dvlpp\Sharp\Config\SharpFormTemplateColumnConfig;

/**
 * Used by SharpEntityConfig and SharpFormTemplateColumnConfig
 *
 * Class HasFormTemplateColumnTrait
 * @package Dvlpp\Sharp\Config\Utils
 */
trait HasFormTemplateColumnTrait
{
    /**
     * @var array
     */
    private $formTemplateColumnsConfig = null;

    /**
     * Add a field in the form.
     *
     * @param SharpFormTemplateColumnConfig $formTemplateColumnConfig
     * @return $this
     */
    final function addFormTemplateColumn(SharpFormTemplateColumnConfig $formTemplateColumnConfig)
    {
        $this->formTemplateColumnsConfig[] = $formTemplateColumnConfig;

        return $this;
    }

    /**
     * @return array
     */
    public function formTemplateColumnsConfig()
    {
        return (array) $this->formTemplateColumnsConfig;
    }
}