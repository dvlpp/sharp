<?php

namespace Dvlpp\Sharp\Config\Utils;

trait HasFormTemplateTrait
{
    /**
     * @var array
     */
    protected $fields = [];

    protected $updateIndexes = [];
    protected $createIndexes = [];

    /**
     * @param string $name
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
            return $this->fields;
        }

        $lookup = ($mode == 1 ? $this->updateIndexes : $this->createIndexes);

        return array_filter($this->fields, function($item, $key) use($lookup) {
            if(is_array($item)) return true;
            return array_search($key, $lookup) !== false;

        }, ARRAY_FILTER_USE_BOTH);
    }
}