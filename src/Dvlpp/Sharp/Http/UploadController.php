<?php

namespace Dvlpp\Sharp\Http;

use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class UploadController extends Controller
{

    public function upload(Request $request)
    {
        try {
            $tab = $this->uploadFile($request);

            return response()->json(["file" => $tab]);

        } catch (\Exception $e) {
            return response()->json(["err" => $e->getMessage()]);
        }
    }

    public function uploadWithThumbnail(Request $request)
    {
        try {
            $tab = $this->uploadFile($request);

            // Manage thumbnail creation
            $tab["thumbnail"] = sharp_thumbnail(
                $tab["path"],
                $request->get("thumbnail_height"),
                $request->get("thumbnail_width")
            );

            return response()->json(["file" => $tab]);

        } catch (\Exception $e) {
            return response()->json(["err" => $e->getMessage()]);
        }

    }

    public function download($fileShortPath)
    {
        $relativeFilePath = config("sharp.upload_storage_base_path") . '/' . $fileShortPath;

        return response()->download(get_file_path($relativeFilePath), basename($relativeFilePath));
    }

    private function uploadFile(Request $request)
    {
        $file = $request->file('file');

        if ($file) {
            $filename = uniqid() . "." . $file->getClientOriginalExtension();
            $filesize = $file->getSize();

            $file->move($this->getTmpUploadDirectory(), $filename);

            return [
                "name" => $filename,
                "size" => $filesize,
                "path" => $this->getTmpUploadDirectory() . "/" . $filename
            ];
        }

        throw new FileNotFoundException;
    }

    private function getTmpUploadDirectory()
    {
        $dir = get_file_path(config("sharp.upload_tmp_base_path"));

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }

} 