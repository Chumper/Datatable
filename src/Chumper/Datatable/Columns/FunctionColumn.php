<?php namespace Chumper\Datatable\Columns;

class FunctionColumn implements ColumnInterface {

    private $callable;

    function __construct($callable)
    {
        $this->callable = $callable;
    }

    public function run($model)
    {
        return call_user_func($this->callable,$model);
    }

}