<?php namespace Dvlpp\Sharp\Config\Entities;

class SharpEntityFormLayout extends HasProperties implements \Iterator, \Countable
{

    use IsIterable;

    protected $structProperties = [
        "__ALL__" => 'Dvlpp\Sharp\Config\Entities\SharpEntityFormLayoutTab'
    ];

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->data);
    }
}

class SharpEntityFormLayoutTab extends HasProperties
{

    protected $structProperties = [
        "col1" => 'Dvlpp\Sharp\Config\Entities\SharpEntityFormLayoutColumn',
        "col2" => 'Dvlpp\Sharp\Config\Entities\SharpEntityFormLayoutColumn'
    ];
}

class SharpEntityFormLayoutColumn extends HasProperties implements \Iterator
{

    use IsIterable;

}

