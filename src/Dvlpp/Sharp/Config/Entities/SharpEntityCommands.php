<?php

namespace Dvlpp\Sharp\Config\Entities;

class SharpEntityCommands extends HasProperties
{
    protected $structProperties = [
        "list" => SharpEntityCommandsList::class,
        "entity" => SharpEntityCommandsList::class
    ];
}

class SharpEntityCommandsList extends HasProperties implements \Iterator
{
    use IsIterable;

    protected $structProperties = [
        "__ALL__" => SharpEntityCommandsListCommand::class
    ];
}

class SharpEntityCommandsListCommand extends HasProperties
{
    protected $mandatoryProperties = ["text", "handler"];
}