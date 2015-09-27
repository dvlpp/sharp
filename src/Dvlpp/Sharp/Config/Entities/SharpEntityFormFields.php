<?php

namespace Dvlpp\Sharp\Config\Entities;

class SharpEntityFormFields extends HasProperties implements \Iterator
{
    use IsIterable;

    protected $structProperties = [
        "__ALL__" => SharpEntityFormField::class
    ];
}


class SharpEntityFormField extends HasProperties
{
    protected $structProperties = [
        "item" => SharpEntityFormFieldListItem::class
    ];
}

class SharpEntityFormFieldListItem extends HasProperties implements \Iterator
{
    use IsIterable;

    protected $structProperties = [
        "__ALL__" => SharpEntityFormField::class
    ];
}