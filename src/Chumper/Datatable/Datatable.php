<?php

namespace Chumper\Datatable;

use Chumper\Datatable\Providers\DTProvider;
use Chumper\Datatable\Composers\DTDataComposer;

class Datatable
{

    /**
     * Will create a new DTDataComposer with the given provider as implementation.
     *
     * @param DTProvider $provider The providder for the underlying data.
     * @return DTDataComposer
     */
    public function make(DTProvider $provider)
    {
        return new DTDataComposer($provider);
    }
}