<?php namespace Chumper\Datatable;

use Chumper\Datatable\Engines\CollectionEngine;
use Chumper\Datatable\Engines\QueryEngine;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Input;
use Request;
use View;

/**
 * Class Datatable
 * @package Chumper\Datatable
 */
class Datatable {

    /**
     * @param $query
     * @return QueryEngine
     */
    public static function query($query)
    {
        return new QueryEngine($query);
    }

    /**
     * @param $collection
     * @return CollectionEngine
     */
    public static function collection($collection)
    {
        return new CollectionEngine($collection);
    }

    /**
     * @return Table
     */
    public static function table()
    {
        return new Table;
    }

    /**
     * @return bool True if the plugin should handle this request, false otherwise
     */
    public static function shouldHandle()
    {
        $echo = Input::get('sEcho',null);
        if(Request::ajax() && !is_null($echo) && is_numeric($echo))
        {
            return true;
        }
        return false;
    }

}