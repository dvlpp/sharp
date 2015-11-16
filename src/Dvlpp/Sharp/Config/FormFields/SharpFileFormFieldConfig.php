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
     * @param array $fileFilter
     * @return $this
     */
    public function setFileFilter($fileFilter)
    {
        $this->fileFilter = $fileFilter;

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
     * @return string
     */
    public function fileFilter()
    {
        return implode(",", $this->fileFilter);
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

}