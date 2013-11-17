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

    /**
     * @var int The column to sort after
     */
    private $orderColumn = -1;

    /**
     * @var mixed Determines the order the result should be sorted after
     */
    private $orderOrder;

    /**
     * @var Collection the resulting collection
     */
    private $collection = null;

    /**
     * @var int Determines if the result should be skipped
     */
    private $skip = 0;

    /**
     * @var int Determines if the result should be taken
     */
    private $take = 0;

    function __construct($builder)
    {
        $this->builder = $builder;
        $this->originalBuilder = clone $builder;
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
        $this->skip = $value;
    }

    public function take($value)
    {
        $this->take = $value;
    }

    public function count()
    {
        $counter = clone $this->builder;
        return $counter->count();
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
        if($this->collection == null)
        {
            if($this->skip > 0)
            {
                $this->builder = $this->builder->skip($this->skip);
            }
            if($this->take > 0)
            {
                $this->builder = $this->builder->take($this->take);
            }
            $this->collection = $this->builder->get();

            if(is_array($this->collection))
                $this->collection = new Collection($this->collection);
        }
        return $this->collection;
    }

    private function doInternalSearch($columns)
    {
        if(empty($this->search))
            return;

        $search = $this->search;
        $this->builder = $this->builder->where(function($query) use ($columns, $search) {
            foreach ($columns as $c) {
                $query->orWhere($c,'like','%'.$search.'%');
            }
        });
    }

    private function compile($columns)
    {
        $this->resultCollection = $this->getCollection()->map(function($row) use ($columns) {
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
                $this->builder = $this->builder->orderBy($col->getName(), $this->orderOrder);
                return;
            }
            $i++;
        }
    }
}