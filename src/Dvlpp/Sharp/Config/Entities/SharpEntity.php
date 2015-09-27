<?php

namespace Dvlpp\Sharp\Config\Entities;

use Dvlpp\Sharp\Config\SharpConfig;
use Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException;

class SharpEntity extends HasProperties
{

    protected $mandatoryProperties = ["label", "repository"];

    protected $defaultPropertiesValues = [
        "id_attribute" => "id",
        "duplicable" => false,
        "state" => false
    ];

    protected $structProperties = [
        "commands" => SharpEntityCommands::class,
        "list_template" => SharpEntityListTemplate::class,
        "form_fields" => SharpEntityFormFields::class,
        "form_layout" => SharpEntityFormLayout::class,
        "events" => SharpEntityEvents::class,
        "state" => SharpEntityState::class,
    ];


    public function __construct($data, $parent)
    {
        parent::__construct($data, $parent);

        if (isset($this->data['extends']) && $this->data['extends']) {
            // This Entity is configured to extend another entity config
            list($cat, $ent) = explode(".", $this->data['extends']);
            $extendedEntity = SharpConfig::findEntity($cat, $ent, false);
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

