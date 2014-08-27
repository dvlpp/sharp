<?php namespace Dvlpp\Sharp\Config\Entities;

class SharpEntityCommands extends HasProperties {

    protected $structProperties = [
        "list" => 'Dvlpp\Sharp\Config\Entities\SharpEntityCommandsList',
        "entity" => 'Dvlpp\Sharp\Config\Entities\SharpEntityCommandsList',
    ];

}

class SharpEntityCommandsList extends HasProperties implements \Iterator {

    use IsIterable;

    protected $structProperties = [
        "__ALL__" => 'Dvlpp\Sharp\Config\Entities\SharpEntityCommandsListCommand'
    ];

}

class SharpEntityCommandsListCommand extends HasProperties {

    protected $mandatoryProperties = ["type", "text", "handler"];

}