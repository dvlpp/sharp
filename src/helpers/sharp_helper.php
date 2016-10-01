<?php

use Dvlpp\Sharp\Config\SharpEntityConfig;
use Intervention\Image\ImageManager;

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

    $sizeMin = isset($params["size_min"]) && $params["size_min"];

    if ($w == 0) {
        $w = null;
    }
    if ($h == 0) {
        $h = null;
    }

    $thumbName = "$relativeFolder/$w-$h"
        . ($sizeMin ? "_min" : "")
        . "/" . basename($source);

    $thumbnailPath = config("sharp.thumbnail_relative_path");

    if(config("sharp.thumbnail_in_storage", false)) {
        $thumbFile = storage_path(
            "app/public/$thumbnailPath/".config('sharp.upload_storage_base_path')."/$thumbName"
        );

    } else {
        $thumbFile = public_path("$thumbnailPath/$thumbName");
    }

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

    if(config("sharp.thumbnail_in_storage", false)) {
        return url("storage/$thumbnailPath/".config('sharp.upload_storage_base_path')."/$thumbName");
    }

    return url("$thumbnailPath/$thumbName");
}

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

/**
 * Generate the entity update form action attribute.
 *
 * @param $category
 * @param $entity
 * @param $instance
 * @return array
 */
function get_entity_update_form_route($category, $entity, $instance)
{
    if ($instance->{$entity->idAttribute()} && !$instance->__sharp_duplication) {
        return [
            "sharp.cms.update",
            $category->key(),
            $entity->key(),
            $instance->{$entity->idAttribute()}
        ];

    } else {
        return [
            "sharp.cms.store",
            $category->key(),
            $entity->key()
        ];
    }
}

/**
 * Returns the value of a given attribute for an entity.
 *
 * @param $instance
 * @param $attributeName
 * @return string
 */
function get_entity_attribute_value($instance, $attributeName)
{
    if (strpos($attributeName, "~")) {
        // If there's a "~" in the field $key, this means we are in a single relation case
        // (One-To-One or Belongs To). The ~ separate the relation name and the value.
        // For instance : boss~name indicate that the instance as a single "boss" relation,
        // which has a "name" attribute.
        list($relation, $attributeName) = explode("~", $attributeName);

        return $instance->{$relation}->{$attributeName};
    } else {
        return $instance->{$attributeName};
    }
}

/**
 * @param SharpEntityConfig $entity
 * @return array
 */
function get_command_forms(SharpEntityConfig $entity)
{
    $commands = [];

    foreach($entity->entityCommandsConfig() as $command) {
        if(!$command->hasForm()) continue;

        $commands[] = $command;
    }

    foreach($entity->listCommandsConfig() as $command) {
        if(!$command->hasForm()) continue;

        $commands[] = $command;
    }

    return $commands;
}

function get_file_path($relativePath, $disk='local')
{
    $storagePath  = Storage::disk($disk)->getDriver()->getAdapter()->getPathPrefix();

    return $storagePath . $relativePath;
}

function key_name_for_form_field($key)
{
    $key = str_replace("~", "-", $key);
    return "sf-$key";
}

function find_available_file_name($relativeDestDir, $fileName, $storageDisk)
{
    $k = 1;
    $baseFileName = $fileName;

    $ext = "";
    if (($pos = strrpos($fileName, '.')) !== false) {
        $ext = substr($fileName, $pos);
        $baseFileName = substr($fileName, 0, $pos);
    }

    while (Storage::disk($storageDisk)->exists("$relativeDestDir/$fileName")) {
        $fileName = $baseFileName . "-" . ($k++) . $ext;
    }

    return $fileName;
}

function normalize_file_name($filename)
{
    return preg_replace("#[^A-Za-z1-9-_\\.]#", "", $filename);
}