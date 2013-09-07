<?php namespace Chumper\Datatable\Engines;

interface EngineInterface {

    const ORDER_ASC = 1;
    const ORDER_DESC = 2;

    public function order($column, $order);
    public function search($value);
    public function skip($value);
    public function take($value);
    public function make($columns);
    public function count();
    public function totalCount();
    public function getArray();
    public function reset();

}