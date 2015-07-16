<?php namespace Dvlpp\Sharp\Config;

use Dvlpp\Sharp\Config\Entities\SharpCategory;
use Dvlpp\Sharp\Config\Entities\SharpEntity;
use Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException;
use Config;

/**
 * Class SharpCmsConfig
 * @package Dvlpp\Sharp\Entities
 *
 * Access to sharp "cms" config file, which contains all CMS data (list columns, form fields, ...)
 */
class SharpCmsConfig {

    /**
     * @var array
     */
    protected static $categories = [];

    /**
     * @param $categoryKey
     * @param $entityKey
     * @param bool $withCache
     * @throws EntityConfigurationNotFoundException
     * @return SharpEntity
     */
    public static function findEntity($categoryKey, $entityKey, $withCache=true)
    {
        $category = self::findCategory($categoryKey, $withCache);

        foreach($category->entities as $entityConfig)
        {
            if($entityConfig == $entityKey)
            {
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
    public static function findCategory($categoryName, $withCache=true)
    {
        if(!$withCache || !array_key_exists($categoryName, SharpCmsConfig::$categories))
        {
            $categoryConfig = Config::get('sharp.cms.'.$categoryName);
            if(!$categoryConfig)
            {
                throw new EntityConfigurationNotFoundException("Category configuration for [$categoryConfig] can't be found");
            }

            $sharpCategorie = new SharpCategory($categoryName, $categoryConfig);

            if(!$withCache)
            {
                return $sharpCategorie;
            }

            SharpCmsConfig::$categories[$categoryName] = $sharpCategorie;
        }

        return SharpCmsConfig::$categories[$categoryName];
    }

    /**
     * @return array
     */
    public static function listCategories()
    {
        $config = Config::get('sharp.cms');
        $tab = [];
        foreach($config as $key => $values)
        {
            if(!array_key_exists($key, SharpCmsConfig::$categories))
            {
                SharpCmsConfig::$categories[$key] = new SharpCategory($key, $values);
            }
            $tab[$key] = SharpCmsConfig::$categories[$key];
        }
        return $tab;
    }
}