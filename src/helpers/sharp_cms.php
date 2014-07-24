<?php

function sharp_thumbnail($source, $w, $h, $params=[])
{
    if(File::exists($source))
    {
        $folder = dirname($source);
        if(Str::startsWith($folder, public_path()))
        {
            $folder = substr($folder, strlen(public_path())+1);
        }

        $sizeMin = isset($params["size_min"]) && $params["size_min"];

        if($w==0) $w=null;
        if($h==0) $h=null;

        $thumbName = "thumbnails/" . $folder . "/$w-$h" .($sizeMin?"_min":"") . "/" . basename($source);
        $thumbFile = public_path($thumbName);

        if(!File::exists($thumbFile))
        {
            // Create thumbnail directories if needed
            if(!File::exists(dirname($thumbFile))) mkdir(dirname($thumbFile), 0777, true);

            $sourceImg = Intervention\Image\Facades\Image::make($source);
            if($sizeMin && $w && $h)
            {
                // This param means $w and $h are minimums. We find which dimension of the original image is the most distant
                // from the wanted size, and we keep this one as constraint
                $dw = $sourceImg->width / $w;
                $dh = $sourceImg->height / $h;
                if($dw>$dh) $w = null;
                else $h = null;
            }

            // Create thumbnail
            $sourceImg->resize($w, $h, function ($constraint) {
                $constraint->aspectRatio();
            })->save($thumbFile);
        }

        return url($thumbName);
    }

    // The source doesn't exist
    return null;
}

function sharp_move_tmp_file($file, $destDir)
{
    $srcFile = public_path("tmp/$file");
    if(File::exists($srcFile))
    {
        if(!File::isDirectory($destDir))
        {
            File::makeDirectory($destDir, 0777, true);
        }

        File::move($srcFile, $destDir."/".$file);
    }
}

function sharp_markdown($text)
{
    return \Michelf\Markdown::defaultTransform($text);
}