<?php namespace Chumper\Datatable;

use Chumper\Datatable\Engines\CollectionEngine;
use Chumper\Datatable\Engines\QueryEngine;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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
     * @param $data Collection|Builder|\Illuminate\Database\Query\Builder
     * @throws Exception
     * @return Api
     */
    public static function from($data)
    {
        if($data instanceof Collection)
            return new Api(new CollectionEngine($data));
        else if($data instanceof \Illuminate\Database\Query\Builder OR $data instanceof Builder)
            return new Api(new QueryEngine($data));

        throw new Exception('The data you provided is not supported: '.get_class($data));
    }

    /**
     * @return Table
     */
    public static function table()
    {
        return new Table;
    }

}