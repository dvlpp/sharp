<?php

namespace Dvlpp\Sharp\Commands\ReturnTypes;

class SharpCommandReturnUrl implements SharpCommandReturn
{
    /**
     * @var string
     */
    private $url;

    /**
     * SharpCommandReturnUrl constructor.
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }


    /**
     * Return an array version of the return
     *
     * @return array
     */
    public function get()
    {
        return [
            "type" => "URL",
            "url" => $this->url
        ];
    }
}