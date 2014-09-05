<?php namespace Chumper\Datatable\Engines;

use Illuminate\Support\Collection;

/**
 * This handles the collections,
 * it needs to compile first, so we wait for the make command and then
 * do all the operations
 *
 * Class CollectionEngine
 * @package Chumper\Datatable\Engines
 */
class CollectionEngine extends BaseEngine {

    /**
     * @var \Illuminate\Support\Collection
     */
    private $workingCollection;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $collection;

    /**
     * @var array Different options
     */
    private $options = array(
        'stripOrder'        =>  false,
        'stripSearch'       =>  false,
        'caseSensitive'     =>  false,
    );

    /**
     * @param Collection $collection
     */
    function __construct(Collection $collection)
    {
        parent::__construct();
        $this->collection = $collection;
        $this->workingCollection = $collection;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->workingCollection->count();
    }

    /**
     * @return int
     */
    public function totalCount()
    {
        return $this->collection->count();
    }

    /**
     * @return array
     */
    public function getArray()
    {
        $this->handleInputs();
        $this->compileArray($this->columns);
        $this->doInternalSearch(new Collection(), array());
        $this->doInternalOrder();

        return array_values($this->workingCollection
            ->slice($this->skip,$this->limit)
            ->toArray()
        );
    }

    /**
     * Resets all operations performed on the collection
     */
    public function reset()
    {
        $this->workingCollection = $this->collection;
        return $this;
    }

    public function stripSearch()
    {
        $this->options['stripSearch'] = true;
        return $this;
    }

    public function stripOrder($callback = true)
    {
        $this->options['stripOrder'] = $callback;
        return $this;
    }

    public function setSearchStrip()
    {
        $this->options['stripSearch'] = true;
        return $this;
    }

    public function setOrderStrip($callback = true)
    {
        return $this->stripOrder($callback);
    }

    public function setCaseSensitive($value)
    {
        $this->options['caseSensitive'] = $value;
        return $this;
    }

    public function getOption($value)
    {
        return $this->options[$value];
    }
    //--------------PRIVATE FUNCTIONS-----------------

    protected function internalMake(Collection $columns, array $searchColumns = array())
    {
        $this->compileArray($columns);
        $this->doInternalSearch($columns, $searchColumns);
        $this->doInternalOrder();

        return $this->workingCollection->slice($this->skip,$this->limit);
    }

    private function doInternalSearch(Collection $columns, array $searchColumns)
    {
        if(is_null($this->search) or empty($this->search))
            return;

        $value = $this->search;
        $caseSensitive = $this->options['caseSensitive'];

        $toSearch = array();

        // Map the searchColumns to the real columns
        $ii = 0;
        foreach($columns as $i => $col)
        {
            if(in_array($columns->get($i)->getName(), $searchColumns))
            {
                $toSearch[] = $ii;
            }
            $ii++;
        }

        $self = $this;
        $this->workingCollection = $this->workingCollection->filter(function($row) use ($value, $toSearch, $caseSensitive, $self)
        {
            for($i = 0; $i < count($row); $i++)
            {
                if(!in_array($i, $toSearch))
                    continue;

                $column = $i;
                if($self->getAliasMapping())
                {
                    $column = $self->getNameByIndex($i);
                }

                if($self->getOption('stripSearch'))
                {
                    $search = strip_tags($row[$column]);
                }
                else
                {
                    $search = $row[$column];
                }
                if($caseSensitive)
                {
                    if($self->exactWordSearch)
                    {
                        if($value === $search)
                            return true;
                    }
                    else
                    {
                        if(str_contains($search,$value))
                            return true;
                    }
                }
                else
                {
                    if($self->getExactWordSearch())
                    {
                        if(strtolower($value) === strtolower($search))
                            return true;
                    }
                    else
                    {
                        if(str_contains(strtolower($search),strtolower($value)))
                            return true;
                    }
                }
            }
        });
    }

    private function doInternalOrder()
    {
        if(is_null($this->orderColumn))
            return;

        $column = $this->orderColumn[0];
        $stripOrder = $this->options['stripOrder'];
        $self = $this;
        $this->workingCollection->sortBy(function($row) use ($column,$stripOrder,$self) {

            if($self->getAliasMapping())
            {
                $column = $self->getNameByIndex($column);
            }
            if($stripOrder)
            {
                if(is_callable($stripOrder)){
                    return $stripOrder($row, $column);
                }else{
                    return strip_tags($row[$column]);
                }
            }
            else
            {
                return $row[$column];
            }
        }, SORT_NATURAL);

        if($this->orderDirection == BaseEngine::ORDER_DESC)
            $this->workingCollection = $this->workingCollection->reverse();
    }

    private function compileArray($columns)
    {
        $self = $this;
        $this->workingCollection = $this->collection->map(function($row) use ($columns, $self) {
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
            $i=0;
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
    }
}
