<?php

namespace Chumper\Datatable;


use Chumper\Datatable\Interfaces\DTProvider;
use Chumper\Datatable\Interfaces\DTDataConfigurer;
use Chumper\Datatable\Interfaces\DTViewConfigurer;

class Datatable
{

    /**
     * @return DTDataConfigurer
     */
    public function data(DTProvider $provider)
    {
        return new DTDataConfigurer($provider);
    }

    /**
     * @return DTViewConfigurer
     */
    public function view()
    {
        return new DTViewConfigurer();
    }

}