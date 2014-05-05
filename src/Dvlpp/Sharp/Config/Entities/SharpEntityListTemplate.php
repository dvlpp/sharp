<?php namespace Dvlpp\Sharp\Config\Entities;


class SharpEntityListTemplate extends HasProperties {

    protected $mandatoryProperties = ["columns"];

    protected $defaultPropertiesValues = [
        "paginate" => false,
        "sortable" => false
    ];

    protected $structProperties = [
        "columns" => 'Dvlpp\Sharp\Config\Entities\SharpEntityListTemplateColumns'
    ];

}

class SharpEntityListTemplateColumns extends HasProperties {

    public function __construct(Array $data)
    {
        parent::__construct($data);
        foreach($data as $key => $field)
        {
            $this->$key = new SharpEntityListTemplateColumn($field);
        }
    }
}

class SharpEntityListTemplateColumn extends HasProperties {

    protected $mandatoryProperties = [];

    protected $defaultPropertiesValues = [
        "sortable" => false,
        "width" => 3
    ];
}