<?php

namespace Dvlpp\Sharp\Repositories\AutoUpdater\Valuators;

use Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException;
use Dvlpp\Sharp\Repositories\SharpEloquentRepositoryUpdaterWithImageAlteration;
use Dvlpp\Sharp\Repositories\SharpEloquentRepositoryUpdaterWithUploads;
use Intervention\Image\ImageManager;

/**
 * Class FileValuator
 * @package Dvlpp\Sharp\Repositories\AutoUpdater\Valuators
 */
class FileValuator implements Valuator
{

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
    private $cropValues;


    /**
     * @param $instance
     * @param $attr
     * @param $data
     * @param $sharpRepository
     * @param $cropValues
     */
    function __construct($instance, $attr, $data, $sharpRepository, $cropValues)
    {
        $this->instance = $instance;
        $this->attr = $attr;
        $this->fileData = $data;
        $this->sharpRepository = $sharpRepository;
        $this->cropValues = $cropValues;
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
                $this->sharpRepository->updateFileUpload($this->instance, $this->attr, $this->fileData);

//            } elseif (trim($this->cropValues)) {
//                // Upload is an image, and there's a crop request
//
//                if (!$this->sharpRepository instanceof SharpEloquentRepositoryUpdaterWithImageAlteration) {
//                    throw new MandatoryClassNotFoundException(
//                        get_class($this->sharpRepository) . ' must implement'
//                        . ' Dvlpp\Sharp\Repositories\SharpEloquentRepositoryUpdaterWithImageAlteration'
//                        . ' to manage auto alteration (crop) of image uploads');
//                }
//
//                $file = $this->instance->getSharpFilePathFor($this->attr);
//
//                $cropVals = explode(",", $this->cropValues);
//                if (sizeof($cropVals) != 4) {
//                    return;
//                }
//
//                $manager = new ImageManager;
//                $img = $manager->make($file);
//
//                $w = (int)($img->width() - $cropVals[0] * $img->width() - ($img->width() - $cropVals[2] * $img->width()));
//                $h = (int)($img->height() - $cropVals[1] * $img->height() - ($img->height() - $cropVals[3] * $img->height()));
//                $x = (int)($cropVals[0] * $img->width());
//                $y = (int)($cropVals[1] * $img->height());
//
//                $img->crop($w, $h, $x, $y);
//
//                $folder = dirname($file);
//                $filename = append_counter_to_filename($file);
//
//                $img->save("$folder/$filename");
//
//                $this->sharpRepository->imageUploadedUpdated($this->instance, $this->attr, $filename);
            }
        }
    }

} 