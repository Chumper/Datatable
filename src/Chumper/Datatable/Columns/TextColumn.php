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
}