<?php

namespace Dvlpp\Sharp\Repositories\AutoUpdater;

trait SharpUploadUpdaterTrait
{

    /**
     * Move the file and return a ["path", "mime", "size"] array.
     *
     * @param $file
     * @param $relativeDestDir
     * @return array|null
     */
    protected function moveUploadedFile($file, $relativeDestDir)
    {
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

        if (\Storage::disk($srcFileDisk)->exists($relativeSrcFile)) {

            // Prepend prefix to destdir (from sharp config)
            $relativeDestDir = config("sharp.upload_storage_base_path") . "/$relativeDestDir";

            // Create storage dir if needed
            if (!\Storage::disk($storageDisk)->exists($relativeDestDir)) {
                \Storage::disk($storageDisk)->makeDirectory($relativeDestDir, 0777, true);
            }

            // Find an available name for the file
            $fileName = $this->findAvailableFileName($relativeDestDir, $fileName, $storageDisk);

            // And finally, copy
            \Storage::disk($storageDisk)->put(
                "$relativeDestDir/$fileName",
                \Storage::disk($srcFileDisk)->get($relativeSrcFile));

            // Get mime and size from the $srcFileDisk: if the storage is in cloud,
            // it will be faster this way.
            $mime = \Storage::disk($srcFileDisk)->mimeType($relativeSrcFile);
            $size = \Storage::disk($srcFileDisk)->size($relativeSrcFile);

            if(!$duplication) {
                \Storage::disk($srcFileDisk)->delete($relativeSrcFile);
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
    protected function findAvailableFileName($relativeDestDir, $fileName, $storageDisk)
    {
        $k = 1;
        $baseFileName = $fileName;

        $ext = "";
        if (($pos = strrpos($fileName, '.')) !== false) {
            $ext = substr($fileName, $pos);
            $baseFileName = substr($fileName, 0, $pos);
        }

        while (\Storage::disk($storageDisk)->exists("$relativeDestDir/$fileName")) {
            $fileName = $baseFileName . "-" . ($k++) . $ext;
        }

        return $fileName;
    }

}