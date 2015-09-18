<?php

namespace Dvlpp\Sharp\Commands\ReturnTypes;

class SharpCommandReturnView implements SharpCommandReturn
{
    /**
     * @var
     */
    private $viewName;
    /**
     * @var array
     */
    private $params;

    /**
     * SharpCommandReturnView constructor.
     * @param $viewName
     * @param array $params
     */
    public function __construct($viewName, array $params = [])
    {
        $this->viewName = $viewName;
        $this->params = $params;
    }


    /**
     * Return an array version of the return
     *
     * @return array
     */
    public function get()
    {
        return [
            "type" => "VIEW"
        ];
    }
}