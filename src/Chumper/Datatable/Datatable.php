<?php namespace Chumper\Datatable;

use Chumper\Datatable\Engines\CollectionEngine;
use Chumper\Datatable\Engines\QueryEngine;

/**
 * Class Datatable
 * @package Chumper\Datatable
 */
class Datatable {

    /**
     * @param $query
     * @return Api
     */
    public static function query($query)
    {
        return new Api(new QueryEngine($query));
    }

    /**
     * @param $collection
     * @return Api
     */
    public static function collection($collection)
    {
        return new Api(new CollectionEngine($collection));
    }

    /**
     * @return Table
     */
    public static function table()
    {
        return new Table;
    }

}