<?php namespace Chumper\Datatable\Engines;

use Assetic\Extension\Twig\AsseticFilterFunction;
use Chumper\Datatable\Columns\DateColumn;
use Chumper\Datatable\Columns\FunctionColumn;
use Chumper\Datatable\Columns\TextColumn;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

/**
 * Class BaseEngine
 * @package Chumper\Datatable\Engines
 */
abstract class BaseEngine {

    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    /**
     * @var mixed
     */
    protected $rowClass = null;

    /**
     * @var mixed
     */
    protected $rowId = null;

    /**
     * @var array
     */
    protected $columnSearches = array();

    /**
     * @var
     */
    private $sEcho;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $columns;

    /**
     * @var array
     */
    private $searchColumns = array();

    /**
     * @var array
     */
    private $showColumns = array();

    /**
     * @var array
     */
    private $orderColumns = array();

    /**
     * @var int
     */
    protected $skip = 0;

    /**
     * @var null
     */
    protected $limit = null;

    /**
     * @var null
     */
    protected $search = null;

    /**
     * @var null
     */
    protected $orderColumn = null;

    /**
     * @var string
     */
    protected $orderDirection = BaseEngine::ORDER_ASC;

    /**
     * @var boolean If the return should be alias mapped
     */
    protected $aliasMapping = false;

    /**
     * @var bool If the search should be done with exact matching
     */
    protected $exactWordSearch = false;


    function __construct()
    {
        $this->columns = new Collection();
        $this->className = str_random(8);
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function addColumn()
    {
        if(func_num_args() != 2 && func_num_args() != 1)
            throw new Exception('Invalid number of arguments');

        if(func_num_args() == 1)
        {
            //add a predefined column
            $this->columns->put(func_get_arg(0)->getName(), func_get_arg(0));
        }
        else if(is_callable(func_get_arg(1)))
        {
            $this->columns->put(func_get_arg(0), new FunctionColumn(func_get_arg(0), func_get_arg(1)));
        }
        else
        {
            $this->columns->put(func_get_arg(0), new TextColumn(func_get_arg(0),func_get_arg(1)));
        }
        return $this;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getColumn($name)
    {
        return $this->columns->get($name,null);
    }

    /**
     * @return array
     */
    public function getOrder()
    {
        return array_keys($this->columns->toArray());
    }

    /**
     * @return array
     */
    public function getOrderingColumns()
    {
        return $this->orderColumns;
    }

    /**
     * @return array
     */
    public function getSearchingColumns()
    {
        return $this->searchColumns;
    }

    /**
     * @return $this
     */
    public function clearColumns()
    {
        $this->columns = new Collection();
        return $this;
    }

    /**
     * @param $cols
     * @return $this
     */
    public function showColumns($cols)
    {
        if ( ! is_array($cols)) {
            $cols = func_get_args();
        }

        foreach ($cols as $property) {
            //quick fix for created_at and updated_at columns
            if(in_array($property, array('created_at', 'updated_at')))
            {
                $this->columns->put($property, new DateColumn($property, DateColumn::DAY_DATE));
            }
            else
            {
                $this->columns->put($property, new FunctionColumn($property, function($model) use($property){return is_array($model)?$model[$property]:$model->$property;}));
            }
            $this->showColumns[] = $property;
        }
        return $this;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function make()
    {
        //TODO Handle all inputs
        $this->handleInputs();
        $this->prepareSearchColumns();

        $output = array(
            "aaData" => $this->internalMake($this->columns, $this->searchColumns)->toArray(),
            "sEcho" => intval($this->sEcho),
            "iTotalRecords" => $this->totalCount(),
            "iTotalDisplayRecords" => $this->count(),
        );
        return Response::json($output);
    }

    /**
     * @param $cols
     * @return $this
     */
    public function searchColumns($cols)
    {
        if ( ! is_array($cols)) {
            $cols = func_get_args();
        }

        $this->searchColumns = array();

        foreach ($cols as $property) {
            $this->searchColumns[] = $property;
        }
        return $this;
    }

    /**
     * @param $cols
     * @return $this
     */
    public function orderColumns($cols)
    {
        if ( ! is_array($cols)) {
            $cols = func_get_args();
        }

        $this->orderColumns = array();

        foreach ($cols as $property) {
            $this->orderColumns[] = $property;
        }
        return $this;
    }

    /**
     * @param $function Set a function for a dynamic row class
     * @return $this
     */
    public function setRowClass($function)
    {
        $this->rowClass = $function;
        return $this;
    }

    /**
     * @param $function Set a function for a dynamic row id
     * @return $this
     */
    public function setRowId($function)
    {
        $this->rowId = $function;
        return $this;
    }

    public function setAliasMapping()
    {
        $this->aliasMapping = true;
        return $this;
    }

    public function setExactWordSearch()
    {
        $this->exactWordSearch = true;
        return $this;
    }

    public function getRowClass()
    {
        return $this->rowClass;
    }

    public function getRowId()
    {
        return $this->rowId;
    }

    public function getAliasMapping()
    {
        return $this->aliasMapping;
    }
    //-------------PRIVATE FUNCTIONS-------------------

    /**
     * @param $value
     */
    private function handleiDisplayStart($value)
    {
        //skip
        $this->skip($value);
    }

    /**
     * @param $value
     */
    private function handleiDisplayLength($value)
    {
        //limit nicht am query, sondern den ganzen
        //holen und dann dynamisch in der Collection taken und skippen
        $this->take($value);
    }

    /**
     * @param $value
     */
    private function handlesEcho($value)
    {
        $this->sEcho = $value;
    }

    /**
     * @param $value
     */
    private function handlesSearch($value)
    {
        //handle search on columns sSearch, bRegex
        $this->search($value);
    }


    /**
     * @param $value
     */
    private function handleiSortCol_0($value)
    {
        if(Input::get('sSortDir_0') == 'desc')
            $direction = BaseEngine::ORDER_DESC;
        else
            $direction = BaseEngine::ORDER_ASC;

        //check if order is allowed
        if(empty($this->orderColumns))
        {
            $this->order($value, $direction);
            return;
        }

        $i = 0;
        foreach($this->columns as $name => $column)
        {
            if($i == $value && in_array($name, $this->orderColumns))
            {
                $this->order($value, $direction);
                return;
            }
            $i++;
        }
    }

    /**
     * @param int $columnIndex
     * @param string $searchValue
     *
     * @return void
     */
    private function handleSingleColumnSearch($columnIndex, $searchValue)
    {
        if (!isset($this->searchColumns[$columnIndex])) return;
        if (empty($searchValue)) return;

        $columnName = $this->searchColumns[$columnIndex];
        $this->searchOnColumn($columnName, $searchValue);
    }

    /**
     *
     */
    protected function handleInputs()
    {
        //Handle all inputs magically
        foreach (Input::all() as $key => $input) {

            // handle single column search
            if ($this->isParameterForSingleColumnSearch($key))
            {
                $columnIndex = str_replace('sSearch_','',$key);
                $this->handleSingleColumnSearch($columnIndex, $input);
                continue;
            }

            if(method_exists($this, $function = 'handle'.$key))
                $this->$function($input);
        }
    }

    /**
     * @param $parameterName
     *
     * @return bool
     */
    private function isParameterForSingleColumnSearch($parameterName)
    {
        static $parameterNamePrefix = 'sSearch_';
        return str_contains($parameterName, $parameterNamePrefix);
    }

    private function prepareSearchColumns()
    {
        if(count($this->searchColumns) == 0 || empty($this->searchColumns))
            $this->searchColumns = $this->showColumns;
    }

    /**
     * @param $column
     * @param $order
     */
    private function order($column, $order = BaseEngine::ORDER_ASC)
    {
        $this->orderColumn = $column;
        $this->orderDirection = $order;
    }

    /**
     * @param $value
     */
    private function search($value)
    {
        $this->search = $value;
    }

    /**
     * @param string $columnName
     * @param mixed $value
     */
    private function searchOnColumn($columnName, $value)
    {
        $this->columnSearches[$columnName] = $value;
    }

    /**
     * @param $value
     */
    private function skip($value)
    {
        $this->skip = $value;
    }

    /**
     * @param $value
     */
    private function take($value)
    {
        $this->limit = $value;
    }

    public function getNameByIndex($index)
    {
        $i = 0;
        foreach($this->columns as $name => $col)
        {
            if($index == $i)
            {
                return $name;
            }
            $i++;
        }
    }

    public function getExactWordSearch()
    {
        return $this->exactWordSearch;
    }

    abstract protected function totalCount();
    abstract protected function count();
    abstract protected function internalMake(Collection $columns, array $searchColumns = array());
} 