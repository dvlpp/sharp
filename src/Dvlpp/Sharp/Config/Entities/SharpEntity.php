<?php namespace Dvlpp\Sharp\Config\Entities;


use Dvlpp\Sharp\Config\SharpCmsConfig;
use Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException;

class SharpEntity extends HasProperties
{

    protected $mandatoryProperties = ["label", "repository"];

    protected $defaultPropertiesValues = [
        "id_attribute" => "id",
        "duplicable" => false
    ];

    protected $structProperties = [
        "commands" => 'Dvlpp\Sharp\Config\Entities\SharpEntityCommands',
        "advanced_search" => 'Dvlpp\Sharp\Config\Entities\SharpEntityAdvancedSearch',
        "list_template" => 'Dvlpp\Sharp\Config\Entities\SharpEntityListTemplate',
        "form_fields" => 'Dvlpp\Sharp\Config\Entities\SharpEntityFormFields',
        "form_layout" => 'Dvlpp\Sharp\Config\Entities\SharpEntityFormLayout',
        "events" => 'Dvlpp\Sharp\Config\Entities\SharpEntityEvents',
    ];


    public function __construct($data, $parent)
    {
        parent::__construct($data, $parent);

        if (isset($this->data['extends']) && $this->data['extends']) {
            // This Entity is configured to extend another entity config
            list($cat, $ent) = explode(".", $this->data['extends']);
            $extendedEntity = SharpCmsConfig::findEntity($cat, $ent, false);
            if (!$extendedEntity) {
                throw new EntityConfigurationNotFoundException("Extended entity [" . $this->data['extends'] . "] configuration not found");
            }

            $entendedEntityData = $extendedEntity->getData();
            $this->data = array_merge($entendedEntityData, $this->data);
        }
    }

    public function getData()
    {
        return $this->data;
    }

}

