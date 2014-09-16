<?php namespace Dvlpp\Sharp\Config;


use Dvlpp\Sharp\Config\Entities\SharpCategory;
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

    public static function findEntity($categoryKey, $entityKey)
    {
        $category = self::findCategory($categoryKey);

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

    public static function findCategory($categoryName)
    {
        if(!array_key_exists($categoryName, SharpCmsConfig::$categories))
        {
            $categoryConfig = Config::get('sharp::cms.'.$categoryName);
            if(!$categoryConfig)
            {
                throw new EntityConfigurationNotFoundException("Category configuration for [$categoryConfig] can't be found");
            }

            SharpCmsConfig::$categories[$categoryName] = new SharpCategory($categoryName, $categoryConfig);
        }

        return SharpCmsConfig::$categories[$categoryName];
    }

    public static function listCategories()
    {
        $config = Config::get('sharp::cms');
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