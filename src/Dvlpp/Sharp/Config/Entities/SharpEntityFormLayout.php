<?php

namespace Dvlpp\Sharp\Config\Entities;

class SharpEntityFormLayout extends HasProperties implements \Iterator, \Countable
{
    use IsIterable;

    protected $structProperties = [
        "__ALL__" => SharpEntityFormLayoutTab::class
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

class SharpEntityFormLayoutTab extends HasProperties implements \Iterator
{
    use IsIterable;
}