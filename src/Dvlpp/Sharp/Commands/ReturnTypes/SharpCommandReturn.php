<?php

namespace Dvlpp\Sharp\Commands\ReturnTypes;

/**
 * A SharpCommandReturn is an object returned by a Sharp Command,
 * to be interpreted by client code.
 *
 * Interface SharpCommandReturn
 * @package Dvlpp\Sharp\Commands\ReturnTypes
 */
interface SharpCommandReturn
{

    /**
     * Return an array version of the return
     *
     * @return array
     */
    public function get();
}