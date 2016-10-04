<?php

namespace Dvlpp\Sharp\Commands\ReturnTypes;

class SharpCommandReturnView implements SharpCommandReturn
{
    /**
     * @var string
     */
    protected $viewName;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var bool
     */
    protected $iframe;

    /**
     * SharpCommandReturnView constructor.
     * @param string $viewName
     * @param array $params
     * @param bool $iframe
     */
    public function __construct($viewName, array $params = [], $iframe = false)
    {
        $this->viewName = $viewName;
        $this->params = $params;
        $this->iframe = $iframe;
    }


    /**
     * Return an array version of the return
     *
     * @return array
     */
    public function get()
    {
        $html = view($this->viewName, $this->params)->render();

        if($this->iframe) {
            $html = '<iframe style="width:100%; height:100%; border: none" srcdoc="'
                . htmlentities($html)
                . '">';
        }

        return [
            "type" => "VIEW",
            "html" => $html
        ];
    }
}