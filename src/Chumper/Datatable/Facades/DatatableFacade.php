<?php

namespace Chumper\Datatable\Facades;

use Illuminate\Support\Facades\Facade;

class DatatableFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Chumper\Datatable\Datatable';
    }
}