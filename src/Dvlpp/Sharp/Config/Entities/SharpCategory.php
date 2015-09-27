<?php

namespace Dvlpp\Sharp\Config\Entities;

class SharpCategory extends HasProperties
{

    protected $mandatoryProperties = ["label", "entities"];

    protected $structProperties = [
        "entities" => SharpEntities::class
    ];

    public function __construct($key, Array $data)
    {
        parent::__construct($data, null);
        $this->key = $key;
    }

}
