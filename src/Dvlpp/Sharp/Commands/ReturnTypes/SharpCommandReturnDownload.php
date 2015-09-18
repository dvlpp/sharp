<?php

namespace Dvlpp\Sharp\Commands\ReturnTypes;

class SharpCommandReturnDownload implements SharpCommandReturn
{
    private $fileName;
    private $filePath;

    /**
     * SharpCommandReturnDownload constructor.
     * @param $fileName
     * @param $filePath
     */
    public function __construct($fileName, $filePath)
    {
        $this->fileName = $fileName;
        $this->filePath = $filePath;
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
            "file_name" => $this->fileName,
            "file_path" => $this->filePath
        ];
    }
}