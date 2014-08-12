<?php namespace Dvlpp\Sharp\Config\Entities;


class SharpEntityFormFields extends HasProperties implements \Iterator {

    use IsIterable;

    protected $structProperties = [
        "__ALL__" => 'Dvlpp\Sharp\Config\Entities\SharpEntityFormField'
    ];
}


class SharpEntityFormField extends HasProperties {

    protected $structProperties = [
        "item" => 'Dvlpp\Sharp\Config\Entities\SharpEntityFormFieldListItem'
    ];

}

class SharpEntityFormFieldListItem extends HasProperties implements \Iterator {

    use IsIterable;

    protected $structProperties = [
        "__ALL__" => 'Dvlpp\Sharp\Config\Entities\SharpEntityFormField'
    ];

}