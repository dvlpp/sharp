<?php namespace Dvlpp\Sharp\Config\Entities;


class SharpEntityAdvancedSearch extends HasProperties
{

    protected $structProperties = [
        "rows" => 'Dvlpp\Sharp\Config\Entities\SharpEntityAdvancedSearchRows'
    ];

}


class SharpEntityAdvancedSearchRows extends HasProperties implements \Iterator
{

    use IsIterable;

    protected $structProperties = [
        "__ALL__" => 'Dvlpp\Sharp\Config\Entities\SharpEntityAdvancedSearchRow'
    ];

}


class SharpEntityAdvancedSearchRow extends HasProperties
{

    protected $structProperties = [
        "fields" => 'Dvlpp\Sharp\Config\Entities\SharpEntityAdvancedSearchFields'
    ];

}


class SharpEntityAdvancedSearchFields extends HasProperties implements \Iterator
{

    use IsIterable;

    protected $structProperties = [
        "__ALL__" => 'Dvlpp\Sharp\Config\Entities\SharpEntityAdvancedSearchField'
    ];

}

class SharpEntityAdvancedSearchField extends HasProperties
{

    protected $mandatoryProperties = ["type"];

}