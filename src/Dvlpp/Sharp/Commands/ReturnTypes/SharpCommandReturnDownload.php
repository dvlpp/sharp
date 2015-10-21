<?php

namespace Dvlpp\Sharp\Commands\ReturnTypes;

class SharpCommandReturnDownload implements SharpCommandReturn
{
    /**
     * @var string
     */
    private $relativeFilePath;

    /**
     * SharpCommandReturnDownload constructor.
     * @param $relativeFilePath
     */
    public function __construct($relativeFilePath)
    {
        $this->relativeFilePath = $relativeFilePath;
    }

    /**
     * Return an array version of the return
     *
     * @return array
     */
    public function get()
    {
        return [
            "type" => "DOWNLOAD",
            "file_path" => $this->relativeFilePath
        ];
    }
}