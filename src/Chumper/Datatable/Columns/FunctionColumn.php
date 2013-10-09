<?php namespace Chumper\Datatable\Columns;

class FunctionColumn implements ColumnInterface {

    private $callable;
    private $name;

    function __construct($callable)
    {
        $this->callable = $callable;
    }

    public function run($model)
    {
        return call_user_func($this->callable,$model);
    }

    /**
     * @param $name String The name of the column
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return String The name of the column
     */
    public function getName()
    {
        return $this->name;
    }
}