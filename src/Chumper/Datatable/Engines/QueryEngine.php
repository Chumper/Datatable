<?php namespace Chumper\Datatable\Engines;

use Illuminate\Database\Query\Builder;

class QueryEngine implements EngineInterface {

    /**
     * @var Builder
     */
    public $builder;
    /**
     * @var Builder
     */
    public $originalBuilder;
    public $search;

    function __construct(Builder $builder)
    {
        $this->builder = $builder;
        $this->originalBuilder = $builder;
    }

    public function order($column, $oder = EngineInterface::ORDER_ASC)
    {
        $this->builder->orderBy($column, $oder);
    }

    public function search($value)
    {
        $this->search = $value;
    }

    public function skip($value)
    {
        $this->builder->skip($value);
    }

    public function take($value)
    {
        $this->builder->take($value);
    }

    public function count()
    {
        return $this->builder->count();
    }

    public function totalCount()
    {
        return $this->originalBuilder->count();
    }

    public function getArray()
    {
        return $this->getCollection()->toArray();
    }

    public function reset()
    {
        $this->builder = $this->originalBuilder;
    }

    public function make($columns, $showColumns = array(), $searchColumns = array())
    {
        $this->doInternalSearch($showColumns);
        return $this->getCollection();
    }

    //--------PRIVATE FUNCTIONS

    private function getCollection()
    {
        return $this->builder->get();
    }

    private function doInternalSearch($columns)
    {
        foreach ($columns as $c) {
            $this->builder->orWhere($c,'like',$this->search);
        }
    }
}