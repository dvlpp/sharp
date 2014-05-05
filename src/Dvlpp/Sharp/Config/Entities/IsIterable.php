<?php namespace Dvlpp\Sharp\Config\Entities;


trait IsIterable {
    private $__iteratorPosition = 0;

    function rewind()
    {
        $this->__iteratorPosition = 0;
    }

    function current()
    {
        return $this->key();
//        return array_values($this->data)[$this->__iteratorPosition];
    }

    function key()
    {
        return array_keys($this->data)[$this->__iteratorPosition];
    }

    function next()
    {
        $this->__iteratorPosition++;
    }

    function valid()
    {
        return sizeof($this->data) > $this->__iteratorPosition;
    }

} 