<?php

use Intervention\Image\ImageManager;

if (!function_exists('sharp_thumbnail')) {
    /**
     * Returns a valid URL to a thumbnail, creating it first if needed.
     *
     * @param $source
     * @param $w
     * @param $h
     * @param array $params
     * @param string|null $relativeFolder
     * @param bool $isTmp
     * @return null|string
     */
    function sharp_thumbnail($source, $w, $h, $params = [], $relativeFolder=null, $isTmp=false)
    {
        if(!$relativeFolder) {
            $relativeFolder = dirname($source);
        }

        if(!$isTmp) {
            // Find out full path of the source file
            $source = config("sharp.upload_storage_base_path") . "/" . $source;
        }

        $thumbnailPath = config("sharp.thumbnail_relative_path");

        $sizeMin = isset($params["size_min"]) && $params["size_min"];

        if ($w == 0) {
            $w = null;
        }
        if ($h == 0) {
            $h = null;
        }

        $thumbName = "$thumbnailPath/$relativeFolder/$w-$h" . ($sizeMin ? "_min" : "") . "/" . basename($source);
        $thumbFile = public_path($thumbName);

        if (!file_exists($thumbFile)) {

            // Create thumbnail directories if needed
            if (!file_exists(dirname($thumbFile))) {
                mkdir(dirname($thumbFile), 0777, true);
            }

            try {
                $disk = $isTmp ? "local" : (config("sharp.upload_storage_disk") ?: "local");

                $manager = new ImageManager;
                $sourceImg = $manager->make(Storage::disk($disk)->get($source));

                if ($sizeMin && $w && $h) {
                    // This param means $w and $h are minimums. We find which dimension of the original image is the most distant
                    // from the wanted size, and we keep this one as constraint
                    $dw = $sourceImg->width() / $w;
                    $dh = $sourceImg->height() / $h;
                    if ($dw > $dh) {
                        $w = null;
                    } else {
                        $h = null;
                    }
                }

                // Create thumbnail
                $sourceImg->resize($w, $h, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($thumbFile, isset($params["quality"]) ? $params["quality"] : null);

            } catch (\Exception $e) {
                return null;
            }
        }

        return url($thumbName);
    }
}

if (!function_exists('sharp_markdown')) {
    /**
     * Markdownify a string
     *
     * @param $text
     * @return mixed
     */
    function sharp_markdown($text)
    {
        return (new Parsedown)->text($text);
    }
}

if (!function_exists('explode_search_words')) {
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
    function explode_search_words(
        $text,
        $isLike = true,
        $handleStar = true,
        $noStarTermPrefix = '%',
        $noStarTermSuffix = '%'
    ) {
        $terms = [];

        foreach (explode(" ", $text) as $term) {
            $term = trim($term);
            if (!$term) {
                continue;
            }

            if ($isLike) {
                if ($handleStar && strpos($term, '*') !== false) {
                    $terms[] = str_replace('*', '%', $term);
                } else {
                    $terms[] = $noStarTermPrefix . $term . $noStarTermSuffix;
                }
            } else {
                $terms[] = $term;
            }
        }

        return $terms;
    }
}