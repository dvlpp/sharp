<?php namespace Dvlpp\Sharp\Http;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UploadController extends Controller {

    public function upload()
    {
        try
        {
            $tab = $this->uploadFile();
            return response()->json(["file"=>$tab]);
        }
        catch(\Exception $e)
        {
            return response()->json(["err"=>$e->getMessage()]);
        }
    }

    public function uploadWithThumbnail(Request $request)
    {
        try
        {
            $tab = $this->uploadFile();

            // Manage thumbnail creation
            $tab["thumbnail"] = sharp_thumbnail(
                $tab["path"],
                $request->get("thumbnail_height"),
                $request->get("thumbnail_width")
            );

            return response()->json(["file"=>$tab]);

        }
        catch(\Exception $e)
        {
            return response()->json(["err"=>$e->getMessage()]);
        }

    }

    private function uploadFile()
    {
        $file = \Input::file('file');

        if($file)
        {
            $filename = uniqid() . "." . $file->getClientOriginalExtension();
            $filesize = $file->getSize();

            $file->move($this->getTmpUploadDirectory(), $filename);

            return [
                "name" => $filename,
                "size" => $filesize,
                "path" =>  $this->getTmpUploadDirectory() . "/" . $filename
            ];
        }

        throw new FileNotFoundException;
    }

    private function getTmpUploadDirectory()
    {
        $dir = \Config::get("sharp.upload_tmp_base_path") ?: storage_path("app/tmp/sharp");
        if( ! \File::exists($dir)) mkdir($dir, 0777, true);

        return $dir;
    }

} 