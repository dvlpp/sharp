<?php namespace Dvlpp\Sharp\Config\Entities;


class SharpEntities extends HasProperties implements \Iterator {

    use IsIterable;

    protected $structProperties = [
        "__ALL__" => 'Dvlpp\Sharp\Config\Entities\SharpEntity'
    ];

}

class SharpEntity extends HasProperties {

    protected $mandatoryProperties = ["label", "repository"];

    protected $structProperties = [
        "list_template" => 'Dvlpp\Sharp\Config\Entities\SharpEntityListTemplate',
        "form_fields" => 'Dvlpp\Sharp\Config\Entities\SharpEntityFormFields'
    ];

}

