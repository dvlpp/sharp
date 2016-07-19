<?php

namespace Dvlpp\Sharp\Repositories\AutoUpdater\Valuators;

use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Jobs\CopyFileInStorage;
use Dvlpp\Sharp\Repositories\SharpEloquentRepositoryUpdaterWithUploads;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Intervention\Image\ImageManager;

/**
 * Class FileValuator
 * @package Dvlpp\Sharp\Repositories\AutoUpdater\Valuators
 */
class FileValuator implements Valuator
{

    use DispatchesJobs;

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
                . SharpEloquentRepositoryUpdaterWithUploads::class
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
        $relativeDestDir = $this->getStorageDirPath();
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
            $relativeDestDir = config("sharp.upload_storage_base_path") . $relativeDestDir;

            // Create storage dir if needed
            if (!$this->fileSystemManager->disk($storageDisk)->exists($relativeDestDir)) {
                $this->fileSystemManager->disk($storageDisk)->makeDirectory($relativeDestDir, 0777, true);

            } else {
                // Find an available name for the file (only if remote dir already exists)
                $fileName = find_available_file_name($relativeDestDir, $fileName, $storageDisk);
            }

            // Get mime and size from the $srcFileDisk: if the storage is in cloud,
            // it will be faster this way.
            $mime = $this->fileSystemManager->disk($srcFileDisk)->mimeType($relativeSrcFile);
            $size = $this->fileSystemManager->disk($srcFileDisk)->size($relativeSrcFile);
            $imageDimensions = null;

            if(starts_with($mime, "image/")) {
                $imageDimensions = $this->getImageDimensions($srcFileDisk, $relativeSrcFile);

                // If there's thumbs to generate, now is the time
                if ($this->fileConfig->thumbnailSize()) {
                    // Generate Sharp's form thumbnail
                    $this->generateThumbnail($relativeSrcFile, $this->fileConfig->thumbnailSize(),
                        $this->getStorageDirPath());
                }

                if ($this->fileConfig->generatedThumbnails()) {
                    // Generate other thumbnails if asked.
                    foreach ($this->fileConfig->generatedThumbnails() as $thumbConfig) {
                        $this->generateThumbnail($relativeSrcFile, $thumbConfig, $this->getStorageDirPath());
                    }
                }
            }

            // And finally, copy. We do this in a Job to authorize queue
            // on configured environments
            $fileCopyJob = new CopyFileInStorage(
                $relativeSrcFile,
                $relativeDestDir . $fileName,
                $srcFileDisk,
                $storageDisk,
                !$duplication);

            if($this->getFileQueueName()) {
                $this->dispatch(
                    $fileCopyJob->onQueue($this->getFileQueueName())
                );

            } else {
                $this->dispatchNow($fileCopyJob);
            }

            return [
                "path" => $relativeDestDir . $fileName,
                "mime" => $mime,
                "size" => $size,
                "is_image" => !is_null($imageDimensions),
                "image_dimensions" => $imageDimensions
            ];
        }

        return null;
    }

    /**
     * Generate a thumbnail with given constraints.
     *
     * @param string $tmpFilePath
     * @param string $thumbConfig
     * @param string $relativeThumbFolder
     */
    private function generateThumbnail($tmpFilePath, $thumbConfig, $relativeThumbFolder)
    {
        if (!str_contains($thumbConfig, "x")) {
            return;
        }

        list($w, $h) = explode("x", $thumbConfig);
        sharp_thumbnail($tmpFilePath, $w, $h, [], $relativeThumbFolder, true);
    }

    private function getFileQueueName()
    {
        return config("sharp.file_queue_name");
    }

    private function getStorageDirPath()
    {
        $path = $this->sharpRepository->getStorageDirPath($this->instance);

        if(!starts_with($path, "/")) {
            $path = "/$path";
        }

        if(!ends_with($path, "/")) {
            $path = "$path/";
        }

        return $path;
    }

    private function getImageDimensions($disk, $file)
    {
        $manager = new ImageManager;
        $img = $manager->make($this->fileSystemManager->disk($disk)->get($file));

        return [$img->width(), $img->height()];
    }
}