<?php

namespace Dvlpp\Sharp\Config\Entities;

class SharpEntityListTemplate extends HasProperties
{
    protected $mandatoryProperties = ["columns"];

    protected $defaultPropertiesValues = [
        "paginate" => false,
        "reorderable" => false,
        "creatable" => true,
        "searchable" => false,
        "sublist" => false
    ];

    protected $structProperties = [
        "columns" => SharpEntityListTemplateColumns::class
    ];

}

class SharpEntityListTemplateColumns extends HasProperties
{

    public function __construct(Array $data)
    {
        parent::__construct($data, $this->parent);
        foreach ($data as $key => $field) {
            $this->$key = new SharpEntityListTemplateColumn($field, $this->parent);
        }
    }
}

class SharpEntityListTemplateColumn extends HasProperties
{
    protected $mandatoryProperties = [];

    protected $defaultPropertiesValues = [
        "sortable" => false,
        "width" => 3
    ];
}