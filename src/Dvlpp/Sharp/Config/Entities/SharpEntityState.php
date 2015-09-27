<?php

namespace Dvlpp\Sharp\Config\Entities;

class SharpEntityState extends HasProperties
{
    protected $mandatoryProperties = ["property"];

    protected $structProperties = [
        "values" => SharpEntityStateValues::class,
    ];
}

class SharpEntityStateValues extends HasProperties implements \Iterator
{
    use IsIterable;

    protected $structProperties = [
        "__ALL__" => SharpEntityStateValuesItem::class
    ];
}

class SharpEntityStateValuesItem extends HasProperties
{
    protected $mandatoryProperties = ["label", "color"];

}