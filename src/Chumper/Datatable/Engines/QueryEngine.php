<?php namespace Chumper\Datatable\Engines;

use Chumper\Datatable\Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

class QueryEngine extends BaseEngine {

    /**
     * @var Builder
     */
    public $builder;
    /**
     * @var Builder
     */
    public $originalBuilder;

    /**
     * @var array single column searches
     */
    public $columnSearches = array();

    /**
     * @var Collection the returning collection
     */
    private $resultCollection;

    /**
     * @var Collection the resulting collection
     */
    private $collection = null;

    /**
     * @var array Different options
     */
    private $options = array(
        'searchOperator'    =>  'LIKE',
        'searchWithAlias'   =>  false,
        'orderOrder'        =>  null,
        'counter'           =>  0,
        'noGroupByOnCount'  =>  false,
        'distinctCountGroup'=>  false,
        'returnQuery'       =>  false,
        'queryKeepsLimits'  =>  false,
    );

    function __construct($builder)
    {
        parent::__construct();
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

    public function count()
    {
        return $this->options['counter'];
    }

    public function totalCount()
    {
        if ($this->options['distinctCountGroup'] && count($this->originalBuilder->groups) == 1)
        {
            $this->originalBuilder->groups = null;
        }
        return $this->originalBuilder->count();
    }

    public function getArray()
    {
       return $this->getCollection($this->builder)->toArray();
    }

    public function reset()
    {
        $this->builder = $this->originalBuilder;
        return $this;
    }


    public function setSearchOperator($value = "LIKE")
    {
        $this->options['searchOperator'] = $value;
        return $this;
    }

    public function setSearchWithAlias($value = true)
    {
        $this->options['searchWithAlias'] = (bool)$value;
        return $this;
    }

    public function setNoGroupByOnCount($value = true)
    {
        $this->options['noGroupByOnCount'] = (bool)$value;
        return $this;
    }

    /**
     * Change the COUNT(*) when there is a group by
     *
     * setDistinctIfGroup will change the count(*) query inside the query builder if it only finds one group by.
     *
     * Instead of counting all of the rows, the distinct rows in the group by will be counted instead.
     *
     * @param bool $value should this option be enabled?
     * @return $this
     */
    public function setDistinctCountGroup($value = true)
    {
        $this->options['distinctCountGroup'] = (bool)$value;
        return $this;
    }

    /**
     * Let internalMake return a QueryBuilder, instead of a collection.
     *
     * @param bool $value
     * @return $this
     */
    public function setReturnQuery($value = true)
    {
        $this->options['returnQuery'] = $value;
        return $this;
    }

    /**
     * Change the behaviour of getQueryBuiler for limits
     *
     * @param bool $value
     * @return $this
     */
    public function setQueryKeepsLimits($value = true)
    {
        $this->options['queryKeepsLimits'] = $value;
        return $this;
    }

    /**
     * Get a Builder object back from the engine. Don't return a collection.
     *
     * @return Query\Builder
     */
    public function getQueryBuilder()
    {
        $this->prepareEngine();
        $this->setReturnQuery();

        return $this->internalMake($this->columns, $this->searchColumns);
    }

    //--------PRIVATE FUNCTIONS

    protected function internalMake(Collection $columns, array $searchColumns = array())
    {
        $builder = clone $this->builder;
        $countBuilder = clone $this->builder;

        $builder = $this->doInternalSearch($builder, $searchColumns);
        $countBuilder = $this->doInternalSearch($countBuilder, $searchColumns);

        if ($this->options['distinctCountGroup'] && count($countBuilder->groups) == 1)
        {
            $countBuilder->select(\DB::raw('COUNT(DISTINCT `' . $countBuilder->groups[0] . '`) as total'));
            $countBuilder->groups = null;

            $results = $countBuilder->get('rows');
            if (isset($results[0]))
            {
                $result = array_change_key_case((array) $results[0]);

            }
            $this->options['counter'] = $result['total'];
        }
        elseif($this->options['searchWithAlias'])
        {
            $this->options['counter'] = count($countBuilder->get());
        }
        else
        {
            if ($this->options['noGroupByOnCount']) {
                $countBuilder->groups = null;
            }
            $this->options['counter'] = $countBuilder->count();
        }

        $builder = $this->doInternalOrder($builder, $columns);

        if ($this->options['returnQuery'])
            if ($this->options['queryKeepsLimits'])
                return $this->getQuery($builder);
            else
                return $builder;

        $collection = $this->compile($builder, $columns);

        return $collection;
    }

    /**
     * @param $builder
     * @return Collection
     */
    private function getQuery($builder)
    {
        // dd($builder->toSql());
        if ($this->collection == null) {
            if ($this->skip > 0) {
                $builder = $builder->skip($this->skip);
            }
            if ($this->limit > 0) {
                $builder = $builder->take($this->limit);
            }
        }

        return $builder;
    }

    private function getCollection($builder)
    {
        $builder = $this->getQuery($builder);

        if ($this->collection == null)
        {
            $this->collection = $builder->get();

            if(is_array($this->collection))
                $this->collection = new Collection($this->collection);
        }
        return $this->collection;
    }

    private function doInternalSearch($builder, $columns)
    {
        if (!empty($this->search)) {
            $builder = $this->buildSearchQuery($builder, $columns);
        }

        if (!empty($this->columnSearches)) {
            $builder = $this->buildSingleColumnSearches($builder);
        }

        return $builder;
    }

    private function buildSearchQuery($builder, $columns)
    {
        $like = $this->options['searchOperator'];
        $search = $this->search;
        $exact = $this->exactWordSearch;
        $builder = $builder->where(function($query) use ($columns, $search, $like, $exact) {
            foreach ($columns as $c) {
                //column to CAST following the pattern column:newType:[maxlength]
                if(strrpos($c, ':')){
                    $c = explode(':', $c);
                    if(isset($c[2]))
                        $c[1] .= "($c[2])";
                    $query->orWhereRaw("cast($c[0] as $c[1]) ".$like." ?", array($exact ? "$search" : "%$search%"));
                }
                else
                    $query->orWhere($c,$like,$exact ? $search : '%'.$search.'%');
            }
        });
        return $builder;
    }

    /**
     * @param $builder
     * Modified by sburkett to facilitate individual exact match searching on individual columns (rather than for all columns)
     */

    private function buildSingleColumnSearches($builder)
    {
      foreach ($this->columnSearches as $index => $searchValue) {
        if(@$this->columnSearchExact[ $this->fieldSearches[$index] ] == 1) {
          $builder->where($this->fieldSearches[$index], '=', $searchValue );
        } else {
          $builder->where($this->fieldSearches[$index], $this->options['searchOperator'], '%' . $searchValue . '%');
        }
      }

    }

    private function compile($builder, $columns)
    {
        $this->resultCollection = $this->getCollection($builder);

        $self = $this;
        $this->resultCollection = $this->resultCollection->map(function($row) use ($columns,$self) {
            $entry = array();
            // add class and id if needed
            if(!is_null($self->getRowClass()) && is_callable($self->getRowClass()))
            {
                $entry['DT_RowClass'] = call_user_func($self->getRowClass(),$row);
            }
            if(!is_null($self->getRowId()) && is_callable($self->getRowId()))
            {
                $entry['DT_RowId'] = call_user_func($self->getRowId(),$row);
            }
            if(!is_null($self->getRowData()) && is_callable($self->getRowData()))
            {
                $entry['DT_RowData'] = call_user_func($self->getRowData(),$row);
            }
            $i = 0;
            foreach ($columns as $col)
            {
                if($self->getAliasMapping())
                {
                    $entry[$col->getName()] =  $col->run($row);
                }
                else
                {
                    $entry[$i] =  $col->run($row);
                }
                $i++;
            }
            return $entry;
        });
        return $this->resultCollection;
    }

    private function doInternalOrder($builder, $columns)
    {
        //var_dump($this->orderColumn);
        if(!is_null($this->orderColumn))
        {
            $i = 0;
            foreach($columns as $col)
            {

                if($i === (int) $this->orderColumn[0])
                {
                    if(strrpos($this->orderColumn[1], ':')){
                        $c = explode(':', $this->orderColumn[1]);
                        if(isset($c[2]))
                            $c[1] .= "($c[2])";
                        $builder = $builder->orderByRaw("cast($c[0] as $c[1]) ".$this->orderDirection);
                    }
                    else
                        $builder = $builder->orderBy($col->getName(), $this->orderDirection);
                    return $builder;
                }
                $i++;
            }
        }
        return $builder;
    }
}
