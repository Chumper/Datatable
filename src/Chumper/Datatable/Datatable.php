<?php namespace Chumper\Datatable;

use Illuminate\Database\Query\Builder;

class Datatable {

    public function api(Builder $builder)
    {
        return new Api($builder);
    }

    public function table()
    {
        return new Table;
    }

}