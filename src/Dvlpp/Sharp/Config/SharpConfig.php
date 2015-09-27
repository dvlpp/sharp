<?php

namespace Dvlpp\Sharp\Config;

use Dvlpp\Sharp\Config\Entities\SharpCategory;
use Dvlpp\Sharp\Config\Entities\SharpEntity;
use Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException;

/**
 * Class SharpCmsConfig
 * @package Dvlpp\Sharp\Entities
 *
 * Access to sharp "cms" config, which contains all CMS data (list columns, form fields, ...)
 */
class SharpConfig
{

    /**
     * @var array
     */
    protected static $categories = [];

    /**
     * Return site name from config.
     *
     * @return string
     */
    public static function getName()
    {
        return config('sharp.name');
    }

    /**
     * Return site languages from config, or null.
     *
     * @return array|null
     */
    public static function getLanguages()
    {
        return config('sharp.languages');
    }

    /**
     * @param $categoryKey
     * @param $entityKey
     * @param bool $withCache
     * @throws EntityConfigurationNotFoundException
     * @return SharpEntity
     */
    public static function findEntity($categoryKey, $entityKey, $withCache = true)
    {
        $category = self::findCategory($categoryKey, $withCache);

        foreach ($category->entities as $entityConfig) {
            if ($entityConfig == $entityKey) {
                $entity = $category->entities->$entityConfig;
                $entity->key = $entityKey;

                return $entity;
            }
        }

        throw new EntityConfigurationNotFoundException("Entity configuration for [$categoryKey.$entityKey] can't be found");
    }

    /**
     * @param $categoryName
     * @param bool $withCache
     * @throws EntityConfigurationNotFoundException
     * @return SharpCategory
     */
    public static function findCategory($categoryName, $withCache = true)
    {
        if (!$withCache || !array_key_exists($categoryName, SharpConfig::$categories)) {
            $categoryConfig = config('sharp.cms.' . $categoryName);
            if (!$categoryConfig) {
                throw new EntityConfigurationNotFoundException("Category configuration for [$categoryConfig] can't be found");
            }

            $sharpCategorie = new SharpCategory($categoryName, $categoryConfig);

            if (!$withCache) {
                return $sharpCategorie;
            }

            SharpConfig::$categories[$categoryName] = $sharpCategorie;
        }

        return SharpConfig::$categories[$categoryName];
    }

    /**
     * @return array
     */
    public static function listCategories()
    {
        $config = config('sharp.cms');
        $tab = [];
        foreach ($config as $key => $values) {
            if (!array_key_exists($key, SharpConfig::$categories)) {
                SharpConfig::$categories[$key] = new SharpCategory($key, $values);
            }
            $tab[$key] = SharpConfig::$categories[$key];
        }

        return $tab;
    }
}