<?php namespace Chumper\Datatable\Engines;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
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

    /**
     * @var int Determines the count of the items
     */
    private $counter = 0;

    function __construct($builder)
    {
        if($builder instanceof Relation)
        {
            $this->builder = $builder->getBaseQuery();
            $this->originalBuilder = clone $builder->getBaseQuery();
        }
        else
        {
            $this->builder = $builder;
            $this->originalBuilder = clone $builder;
        }
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
        return $this->counter;
    }

    public function totalCount()
    {
        return $this->originalBuilder->count();
    }

    public function getArray()
    {
       return $this->getCollection($this->builder)->toArray();
    }

    public function reset()
    {
        $this->builder = $this->originalBuilder;
    }

    public function make(Collection $columns, array $searchColumns = array())
    {
        $builder = clone $this->builder;
        $builder = $this->doInternalSearch($builder, $searchColumns);
        $builder = $this->doInternalOrder($builder, $columns);
        return $this->compile($builder, $columns);
    }

    //--------PRIVATE FUNCTIONS

    /**
     * @param $builder
     * @return Collection
     */
    private function getCollection($builder)
    {
        if($this->collection == null)
        {
            if($this->skip > 0)
            {
                $builder = $builder->skip($this->skip);
            }
            if($this->take > 0)
            {
                $builder = $builder->take($this->take);
            }
            $this->collection = $builder->get();

            if(is_array($this->collection))
                $this->collection = new Collection($this->collection);
        }
        return $this->collection;
    }

    private function doInternalSearch($builder, $columns)
    {
        if(empty($this->search))
            return $builder;

        $search = $this->search;
        $builder = $builder->where(function($query) use ($columns, $search) {
            foreach ($columns as $c) {
                $query->orWhere($c,'like','%'.$search.'%');
            }
        });
        return $builder;
    }

    private function compile($builder, $columns)
    {
        $this->counter = $builder->count();

        $this->resultCollection = $this->getCollection($builder)->map(function($row) use ($columns) {
            $entry = array();
            foreach ($columns as $col)
            {
                $entry[] =  $col->run($row);
            }
            return $entry;
        });
        return $this->resultCollection;
    }

    private function doInternalOrder($builder, $columns)
    {
        $i = 0;
        foreach($columns as $col)
        {
            if($i == $this->orderColumn)
            {
                $builder = $builder->orderBy($col->getName(), $this->orderOrder);
                return $builder;
            }
            $i++;
        }
        return $builder;
    }

    public function setSearchStrip()
    {
        // can not be implemented with the Query engine!
    }

    public function setOrderStrip()
    {
        // can not be implemented with the Query engine!
    }
}