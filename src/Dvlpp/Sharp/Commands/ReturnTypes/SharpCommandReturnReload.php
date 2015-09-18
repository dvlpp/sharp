<?php

namespace Dvlpp\Sharp\Commands\ReturnTypes;

class SharpCommandReturnReload implements SharpCommandReturn
{

    /**
     * Return an array version of the return
     *
     * @return array
     */
    public function get()
    {
        return [
            "type" => "RELOAD"
        ];
    }
}