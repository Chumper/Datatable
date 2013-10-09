<?php namespace Chumper\Datatable\Engines;

interface EngineInterface {

    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    public function order($column, $order);
    public function search($value);
    public function skip($value);
    public function take($value);
    public function make($columns, $showColumns = array(), $searchColumns = array());
    public function count();
    public function totalCount();
    public function getArray();
    public function reset();

}