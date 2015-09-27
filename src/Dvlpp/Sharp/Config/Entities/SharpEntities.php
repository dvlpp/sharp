<?php

namespace Dvlpp\Sharp\Config\Entities;

class SharpEntities extends HasProperties implements \Iterator
{
    use IsIterable;

    protected $structProperties = [
        "__ALL__" => SharpEntity::class
    ];

}

