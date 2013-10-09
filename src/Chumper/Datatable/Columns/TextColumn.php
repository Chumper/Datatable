<?php namespace Chumper\Datatable\Columns;

class TextColumn implements ColumnInterface {

    private $text;

    function __construct($text)
    {
        $this->text = $text;
    }

    public function run($model)
    {
        return $this->text;
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