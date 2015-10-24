<?php
/**
 * Created by PhpStorm.
 * User: n.plaschke
 * Date: 24/10/15
 * Time: 21:14
 */

namespace Chumper\Datatable\Facades;

use Illuminate\Support\Facades\Facade;

class DatatableFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Chumper\Datatable\Datatable';
    }
}