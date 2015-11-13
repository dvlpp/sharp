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
     * @var string
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

}