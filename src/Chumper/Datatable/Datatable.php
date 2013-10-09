<?php namespace Chumper\Datatable;

use Chumper\Datatable\Engines\CollectionEngine;
use Chumper\Datatable\Engines\QueryEngine;
use Exception;
use Illuminate\Database\Query\Builder;
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
     * @param $data Collection|Builder
     * @throws Exception
     * @return Api
     */
    public function from($data)
    {
        if(is_subclass_of($data, 'Illuminate\Support\Collection'))
            return new Api(new CollectionEngine($data));
        if(is_subclass_of($data, 'Illuminate\Database\Query\Builder'))
            return new Api(new CollectionEngine($data));

        throw new Exception('The data you provided is not supported: '.class_basename($data));
    }

    /**
     * @return Table
     */
    public static function table()
    {
        return new Table;
    }

}