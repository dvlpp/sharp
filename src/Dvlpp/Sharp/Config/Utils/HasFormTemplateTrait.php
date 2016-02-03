<?php

namespace Dvlpp\Sharp\Config\Utils;

trait HasFormTemplateTrait
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    private $updateIndexes = [];

    /**
     * @var array
     */
    private $createIndexes = [];

    /**
     * @param string $name
     * @param bool $update
     * @param bool $creation
     * @return $this
     */
    public function addField($name, $update=true, $creation=true)
    {
        if($update) {
            $this->updateIndexes[] = sizeof($this->fields);
        }

        if($creation) {
            $this->createIndexes[] = sizeof($this->fields);
        }

        $this->fields[] = $name;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function addFieldUpdateOnly($name)
    {
        return $this->addField($name, true, false);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function addFieldCreationOnly($name)
    {
        return $this->addField($name, false, true);
    }

    /**
     * @param string $name
     * @param array $fieldNames
     * @return $this
     */
    public function addFieldset($name, $fieldNames)
    {
        $this->fields[$name] = $fieldNames;

        return $this;
    }

    /**
     * @param int $mode 1 for update, 2 for creation, 3 for both
     * @return array
     */
    public function fields($mode)
    {
        if($mode == 3) {
            // Return all fields
            return $this->fields;
        }

        $lookup = ($mode == 1 ? $this->updateIndexes : $this->createIndexes);

        // PHP 5.5
        $fields = [];
        foreach($this->fields as $key => $item) {
            if(is_array($item) || array_search($key, $lookup) !== false) {
                $fields[] = $item;
            }
        }
        return $fields;

        // PHP 5.6: we can use ARRAY_FILTER_USE_BOTH
        // http://php.net/manual/fr/function.array-filter.php
//        return array_filter($this->fields, function($item, $key) use($lookup) {
//            if(is_array($item)) return true;
//            return array_search($key, $lookup) !== false;
//
//        }, ARRAY_FILTER_USE_BOTH);
    }
}