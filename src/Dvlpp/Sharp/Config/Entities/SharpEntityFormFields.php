<?php namespace Dvlpp\Sharp\Config\Entities;


class SharpEntityFormFields extends HasProperties implements \Iterator {

    use IsIterable;

    protected $structProperties = [
        "__ALL__" => 'Dvlpp\Sharp\Config\Entities\SharpEntityFormField'
    ];
}

/*class SharpEntityFormFields extends HasProperties {

    protected $structProperties = [
        "col1" => 'Dvlpp\Sharp\Config\Entities\SharpEntityFormFieldsColumn',
        "col2" => 'Dvlpp\Sharp\Config\Entities\SharpEntityFormFieldsColumn'
    ];
}

class SharpEntityFormFieldsColumn extends HasProperties implements \Iterator {

    use IsIterable;

    protected $structProperties = [
        "__ALL__" => 'Dvlpp\Sharp\Config\Entities\SharpEntityFormField'
    ];

}*/


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