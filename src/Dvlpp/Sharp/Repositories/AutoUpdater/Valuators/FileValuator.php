<?php

namespace Dvlpp\Sharp\Repositories\AutoUpdater\Valuators;

use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Repositories\SharpEloquentRepositoryUpdaterWithUploads;
use Illuminate\Contracts\Filesystem\Factory;

/**
 * Class FileValuator
 * @package Dvlpp\Sharp\Repositories\AutoUpdater\Valuators
 */
class FileValuator implements Valuator
{
    /**
     * @var Factory
     */
    protected $fileSystemManager;

    /**
     * @var
     */
    private $instance;

    /**
     * @var string
     */
    private $attr;

    /**
     * @var string
     */
    private $fileData;

    /**
     * @var SharpEloquentRepositoryUpdaterWithUploads
     */
    private $sharpRepository;
    /**
     * @var
     */
    private $fileConfig;


    /**
     * @param $instance
     * @param $attr
     * @param $data
     * @param $fileConfig
     * @param $sharpRepository
     */
    function __construct($instance, $attr, $data, $fileConfig, $sharpRepository)
    {
        $this->instance = $instance;
        $this->attr = $attr;
        $this->fileData = $data;
        $this->sharpRepository = $sharpRepository;
        $this->fileConfig = $fileConfig;

        $this->fileSystemManager = app(Factory::class);
    }

    /**
     * Valuate the field
     */
    public function valuate()
    {
        if (!$this->sharpRepository instanceof SharpEloquentRepositoryUpdaterWithUploads) {
            throw new MandatoryClassNotFoundException(
                get_class($this->sharpRepository) . ' must implement'
                . ' Dvlpp\Sharp\Repositories\SharpEloquentRepositoryUpdaterWithUploads'
                . ' to manage auto update of file uploads');
        }

        if (!$this->fileData && $this->instance->{$this->attr}) {
            // Delete
            $this->sharpRepository->deleteFileUpload($this->instance, $this->attr);

        } elseif ($this->fileData && $this->fileData != ":DUPL:") {
            if ($this->fileData != $this->instance->{$this->attr}) {
                // Update (or create)

                // First move or copy the file
                $moveResult = $this->moveUploadedFile();

                // Then call the project repo to store the file information in DB
                if($moveResult) {
                    $this->sharpRepository->updateFileUpload($this->instance, $this->attr, $moveResult);
                }

            }
        }
    }

    private function moveUploadedFile()
    {
        $relativeDestDir = $this->sharpRepository->getStorageDirPath($this->instance);
        $file = $this->fileData;
        $storageDisk = config("sharp.upload_storage_disk") ?: "local";

        if (starts_with($file, ":DUPL:")) {
            // Duplication case: file in on the storage disk
            $duplication = true;
            $file = substr($file, strlen(":DUPL:"));
            $relativeSrcFile = config("sharp.upload_storage_base_path") . "/$file";
            $srcFileDisk = $storageDisk;

        } else {
            // File is in tmp dir
            $duplication = false;
            $relativeSrcFile = config("sharp.upload_tmp_base_path") . "/$file";
            $srcFileDisk = 'local';
        }

        $fileName = basename($file);

        if ($this->fileSystemManager->disk($srcFileDisk)->exists($relativeSrcFile)) {

            // Prepend prefix to destdir (from sharp config)
            $relativeDestDir = config("sharp.upload_storage_base_path") . "/$relativeDestDir";

            // Create storage dir if needed
            if (!$this->fileSystemManager->disk($storageDisk)->exists($relativeDestDir)) {
                $this->fileSystemManager->disk($storageDisk)->makeDirectory($relativeDestDir, 0777, true);
            }

            // Find an available name for the file
            $fileName = $this->findAvailableFileName($relativeDestDir, $fileName, $storageDisk);

            // And finally, copy
            $this->fileSystemManager->disk($storageDisk)->put(
                "$relativeDestDir/$fileName",
                $this->fileSystemManager->disk($srcFileDisk)->get($relativeSrcFile));

            // Get mime and size from the $srcFileDisk: if the storage is in cloud,
            // it will be faster this way.
            $mime = $this->fileSystemManager->disk($srcFileDisk)->mimeType($relativeSrcFile);
            $size = $this->fileSystemManager->disk($srcFileDisk)->size($relativeSrcFile);

            // If there's thumbs to generate, now is the time
            if($this->fileConfig->generate_thumbs) {
                foreach($this->fileConfig->generate_thumbs as $thumbConfig) {
                    $this->generateThumbnail($this->fileData, $thumbConfig);
                }
            }

            if(!$duplication) {
                $this->fileSystemManager->disk($srcFileDisk)->delete($relativeSrcFile);
            }

            return [
                "path" => "$relativeDestDir/$fileName",
                "mime" => $mime,
                "size" => $size
            ];
        }

        return null;
    }

    /**
     * @param $relativeDestDir
     * @param $fileName
     * @param $storageDisk
     * @return string
     */
    private function findAvailableFileName($relativeDestDir, $fileName, $storageDisk)
    {
        $k = 1;
        $baseFileName = $fileName;

        $ext = "";
        if (($pos = strrpos($fileName, '.')) !== false) {
            $ext = substr($fileName, $pos);
            $baseFileName = substr($fileName, 0, $pos);
        }

        while ($this->fileSystemManager->disk($storageDisk)->exists("$relativeDestDir/$fileName")) {
            $fileName = $baseFileName . "-" . ($k++) . $ext;
        }

        return $fileName;
    }

    private function generateThumbnail($fileData, $thumbConfig)
    {

    }

} 