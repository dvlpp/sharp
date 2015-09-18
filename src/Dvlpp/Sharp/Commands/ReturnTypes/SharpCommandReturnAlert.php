<?php

namespace Dvlpp\Sharp\Commands\ReturnTypes;

class SharpCommandReturnAlert implements SharpCommandReturn
{
    private $title;
    private $message;
    private $level;

    /**
     * SharpCommandReturnAlert constructor.
     * @param $title
     * @param $message
     * @param $level
     */
    public function __construct($title, $message, $level)
    {
        $this->title = $title;
        $this->message = $message;
        $this->level = $level;
    }

    /**
     * Return an array version of the return
     *
     * @return array
     */
    public function get()
    {
        return [
            "type" => "ALERT",
            "title" => $this->title,
            "level" => $this->level,
            "message" => $this->message
        ];
    }
}