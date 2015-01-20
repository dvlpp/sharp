<?php

use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

if ( ! function_exists('sharp_thumbnail'))
{
    /**
     * Returns a valid URL to a thumbnail, creating it first if needed.
     *
     * @param $source
     * @param $w
     * @param $h
     * @param array $params
     * @return null|string
     */
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

                $manager = new ImageManager;
                $sourceImg = $manager->make($source);

                if($sizeMin && $w && $h)
                {
                    // This param means $w and $h are minimums. We find which dimension of the original image is the most distant
                    // from the wanted size, and we keep this one as constraint
                    $dw = $sourceImg->width() / $w;
                    $dh = $sourceImg->height() / $h;
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
}

if ( ! function_exists('sharp_markdown'))
{
    /**
     * Markdownify a string
     *
     * @param $text
     * @return mixed
     */
    function sharp_markdown($text)
    {
        return (new Parsedown)->text($text);
//        return \Michelf\Markdown::defaultTransform($text);
    }
}

if ( ! function_exists('explode_search_words'))
{
    /**
     * Return an array of words from a given string, with optional prefix / suffix.
     *
     * @param $text
     * @param bool $isLike
     * @param bool $handleStar
     * @param string $noStarTermPrefix
     * @param string $noStarTermSuffix
     * @return array
     */
    function explode_search_words($text, $isLike=true, $handleStar=true, $noStarTermPrefix='%', $noStarTermSuffix='%')
    {
        $terms = [];

        foreach(explode(" ", $text) as $term)
        {
            $term = trim($term);
            if(!$term) continue;

            if($isLike)
            {
                if($handleStar && strpos($term, '*') !== false)
                {
                    $terms[] = str_replace('*', '%', $term);
                }
                else
                {
                    $terms[] = $noStarTermPrefix . $term . $noStarTermSuffix;
                }
            }
            else
            {
                $terms[] = $term;
            }
        }

        return $terms;
    }
}

if ( ! function_exists('sharp_encode_embedded_entity_data'))
{
    /**
     * @param $data
     * @return string
     */
    function sharp_encode_embedded_entity_data($data)
    {
        return base64_encode(serialize($data));
    }
}

if ( ! function_exists('sharp_decode_embedded_entity_data'))
{
    /**
     * @param $data
     * @return array
     */
    function sharp_decode_embedded_entity_data($data)
    {
        return unserialize(base64_decode($data));
    }
}