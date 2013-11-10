<?php namespace Chumper\Datatable\Engines;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class QueryEngine implements EngineInterface {

    /**
     * @var Builder
     */
    public $builder;
    /**
     * @var Builder
     */
    public $originalBuilder;

    /**
     * @var String search term
     */
    public $search;

    /**
     * @var Collection the returning collection
     */
    private $resultCollection;

    private $orderColumn = -1;

    private $orderOrder;

    function __construct($builder)
    {
        $this->builder = $builder;
        $this->originalBuilder = $builder;
    }

    public function order($column, $oder = EngineInterface::ORDER_ASC)
    {
        $this->orderColumn = $column;
        $this->orderOrder = $oder;
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

    public function make(Collection $columns, array $searchColumns = array())
    {
        $this->doInternalSearch($searchColumns);
        $this->doInternalOrder($columns);
        return $this->compile($columns);
    }

    //--------PRIVATE FUNCTIONS

    /**
     * @return Collection
     */
    private function getCollection()
    {
        $result = $this->builder->get();
        if(is_array($result))
            return new Collection($result);
        return $result;
    }

    private function doInternalSearch($columns)
    {
        if(empty($this->search))
            return;

        foreach ($columns as $c) {
            $this->builder->orWhere($c,'like','%'.$this->search.'%');
        }
    }

    private function compile($columns)
    {
        $result = $this->getCollection();
        $this->resultCollection = $result->map(function($row) use ($columns) {
            $entry = array();
            foreach ($columns as $col)
            {
                $entry[] =  $col->run($row);
            }
            return $entry;
        });
        return $this->resultCollection;
    }

    private function doInternalOrder(Collection $columns)
    {
        $i = 0;
        foreach($columns as $col)
        {
            if($i == $this->orderColumn)
            {
                $this->builder->orderBy($col->getName(), $this->orderOrder);
                return;
            }
            $i++;
        }
    }
}