<?php

namespace Dvlpp\Sharp\Config\FormFields;

use Dvlpp\Sharp\Config\SharpFormFieldConfig;

class SharpFileFormFieldConfig extends SharpFormFieldConfig
{
    /**
     * @var int
     */
    protected $maxFileSize;

    /**
     * @var array
     */
    protected $fileFilter;

    /**
     * @var string
     */
    protected $thumbnailSize;

    /**
     * @var array
     */
    protected $generatedThumbnails = [];

    /**
     * @var string
     */
    protected $fileFilterAlertMessage;

    /**
     * @param string $key
     * @return static
     */
    public static function create($key)
    {
        $instance = new static;
        $instance->key = $key;

        $instance->maxFileSize = 5;
        $instance->fileFilter = "";
        $instance->label = "";

        return $instance;
    }

    /**
     * Set max file size (in MB)
     *
     * @param int $size
     * @return $this
     */
    public function setMaxFileSize($size)
    {
        $this->maxFileSize = $size;

        return $this;
    }

    /**
     * @param array|string $fileFilter
     * @return $this
     */
    public function setFileFilter($fileFilter)
    {
        if(is_string($fileFilter)) {
            $fileFilter = explode(",", $fileFilter);
        }

        $this->fileFilter = [];
        foreach($fileFilter as $fileExt) {
            if(!starts_with($fileExt, ".")) {
                $fileExt = ".$fileExt";
            }
            $this->fileFilter[] = trim($fileExt);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setFileFilterImages()
    {
        $this->fileFilter = [".jpg", ".jpeg", ".gif", ".png"];

        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setFileFilterAlertMessage($message)
    {
        $this->fileFilterAlertMessage = $message;

        return $this;
    }

    /**
     * @param string $size
     * @return $this
     */
    public function setThumbnail($size)
    {
        $this->thumbnailSize = $size;

        return $this;
    }

    /**
     * @param string $size
     * @return $this
     */
    public function addGeneratedThumbnail($size)
    {
        $this->generatedThumbnails[] = $size;

        return $this;
    }

    public function type()
    {
        return "file";
    }

    /**
     * @return int
     */
    public function maxFileSize()
    {
        return $this->maxFileSize;
    }

    /**
     * @return array
     */
    public function fileFilter()
    {
        return $this->fileFilter;
    }

    /**
     * @return string
     */
    public function thumbnailSize()
    {
        return $this->thumbnailSize;
    }

    /**
     * @return string
     */
    public function fileFilterAlertMessage()
    {
        return $this->fileFilterAlertMessage;
    }

    public function generatedThumbnails()
    {
        return $this->generatedThumbnails;
    }

}