<?php

namespace Dvlpp\Sharp\Http;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        try {
            $tab = $this->uploadFile($request);

            return response()->json(["file" => $tab]);

        } catch (\Exception $e) {
            return response()->json(["err" => $e->getMessage()]);
        }
    }

    /**
     * @param $fileShortPath
     * @return Response
     * @throws FileNotFoundException
     */
    public function download($fileShortPath)
    {
        if(strstr($fileShortPath, ":")) {
            list($disk, $path) = explode(":", $fileShortPath);
        } else {
            $disk = config("sharp.upload_storage_disk");
            $path = $fileShortPath;
        }

        if(!starts_with($path, "/")) {
            $path = config("sharp.upload_storage_base_path") . "/" . $path;
        }

        $response = new Response(Storage::disk($disk)->get($path), 200, [
            "Content-Type" => Storage::disk($disk)->mimeType($path),
            "Content-Disposition" => "attachment"
        ]);

        ob_end_clean();

        return $response;
    }

    private function uploadFile(Request $request)
    {
        $file = $request->file('file');

        if ($file) {
            $filename = find_available_file_name(
                $this->getTmpUploadDirectory(),
                normalize_file_name($file->getClientOriginalName()),
                "local"
            );

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