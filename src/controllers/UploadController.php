<?php

class UploadController extends BaseController {

    public function upload()
    {
        try
        {
            $tab = $this->_uploadFile();
            return Response::json(["file"=>$tab]);
        }
        catch(Exception $e)
        {
            return Response::json(["err"=>$e->getMessage()]);
        }
    }

    public function uploadWithThumbnail()
    {
        try
        {
            $tab = $this->_uploadFile();

            // Manage thumbnail creation
            $th = Input::get("thumbnail_height");
            $tw = Input::get("thumbnail_width");
            $file = public_path('tmp') . "/" . $tab['name'];
            $thumb = sharp_thumbnail($file, $tw, $th);
            $tab["thumbnail"] = $thumb;

            return Response::json(["file"=>$tab]);
        }
        catch(Exception $e)
        {
            return Response::json(["err"=>$e->getMessage()]);
        }

    }

    private function _uploadFile()
    {
        $file = Input::file('file');
        if($file)
        {
            $filename = uniqid() . "." . $file->getClientOriginalExtension();
            $filesize = $file->getSize();
            $file->move(public_path('tmp'), $filename);
            return ["name"=>$filename, "size"=>$filesize, "path"=>public_path('tmp/'.$filename)];
        }
        throw new Exception("Fichier introuvable");
    }

} 