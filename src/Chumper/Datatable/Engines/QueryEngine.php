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
     * @var array single column searches
     */
    public $columnSearches = array();

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

    /**
     * @var boolean Determines if the alias should be allowed in the search
     */
    private $searchWithAlias = false;

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

    public function columnSearch($columnName, $value)
    {
        $this->columnSearches[$columnName] = $value;
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
        $countBuilder = clone $this->builder;

        $builder = $this->doInternalSearch($builder, $searchColumns);
        $countBuilder = $this->doInternalSearch($countBuilder, $searchColumns);

        if($this->searchWithAlias)
        {
            $this->counter = count($countBuilder->get());
        }
        else
        {
            $this->counter = $countBuilder->count();
        }

        $builder = $this->doInternalOrder($builder, $columns);
        $collection = $this->compile($builder, $columns);

        return $collection;
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
            //dd($this->builder->toSql());
            $this->collection = $builder->get();

            if(is_array($this->collection))
                $this->collection = new Collection($this->collection);
        }
        return $this->collection;
    }

    private function doInternalSearch($builder, $columns)
    {
        if (!empty($this->search)) {
            $this->buildSearchQuery($builder, $columns);
        }

        if (!empty($this->columnSearches)) {
            $this->buildSingleColumnSearches($builder);
        }

        return $builder;
    }

    private function buildSearchQuery($builder, $columns)
    {
        $search = $this->search;
        $builder->where(function($query) use ($columns, $search) {
            foreach ($columns as $c) {
                $query->orWhere($c,'like','%'.$search.'%');
            }
        });
    }

    private function buildSingleColumnSearches($builder)
    {
        foreach ($this->columnSearches as $columnName => $searchValue) {
            $builder->where($columnName, 'like', '%' . $searchValue . '%');
        }
    }

    private function compile($builder, $columns)
    {
        $this->resultCollection = $this->getCollection($builder);

        $this->resultCollection = $this->resultCollection->map(function($row) use ($columns) {
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

    public function setSearchWithAlias()
    {
        $this->searchWithAlias = true;
    }
}
