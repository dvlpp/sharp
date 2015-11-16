<?php

use Dvlpp\Sharp\Config\SharpCategoryConfig;
use Dvlpp\Sharp\Config\SharpEntityConfig;

function sharp_site_name()
{
    return config("sharp.name");
}

function sharp_languages()
{
    return config("sharp.languages");
}

function sharp_categories()
{
    if(!config("sharp.categories")) return [];

    $categories = [];
    foreach(config("sharp.categories") as $categoryKey) {
        $categories[$categoryKey] = app("sharp.$categoryKey");
    };

    return $categories;
}

/**
 * @param string $categoryKey
 * @return SharpCategoryConfig
 */
function sharp_category($categoryKey)
{
    $category = app("sharp.$categoryKey");

    if($category) {
        $category->setKey($categoryKey);
    }

    return $category;
}

/**
 * @param string $categoryKey
 * @param string $entityKey
 * @return SharpEntityConfig
 */
function sharp_entity($categoryKey, $entityKey)
{
    $entity = app("sharp.$categoryKey.$entityKey");

    if($entity) {
        $entity->setCategoryKey($categoryKey);
        $entity->setKey($entityKey);
    }

    return $entity;
}