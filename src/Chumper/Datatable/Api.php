<?php namespace Chumper\Datatable;

use Chumper\Datatable\Columns\DateColumn;
use Chumper\Datatable\Columns\FunctionColumn;
use Chumper\Datatable\Columns\TextColumn;
use Chumper\Datatable\Engines\EngineInterface;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

/**
 * Class Api
 * @package Chumper\Datatable
 */
class Api {

    /**
     * @var
     */
    private $sEcho;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $columns;

    /**
     * @var Engines\EngineInterface
     */
    private $engine;

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
     * @param EngineInterface $engine
     */
    function __construct(EngineInterface $engine)
    {
        $this->columns = new Collection();
        $this->engine = $engine;
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
            "aaData" => $this->engine->make($this->columns, $this->searchColumns)->toArray(),
            "sEcho" => intval($this->sEcho),
            "iTotalRecords" => $this->engine->totalCount(),
            "iTotalDisplayRecords" => $this->engine->count(),
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

    public function stripSearchColumns()
    {
        $this->engine->setSearchStrip();
        return $this;
    }

    public function stripOrderColumns()
    {
        $this->engine->setOrderStrip();
        return $this;
    }

    public function setSearchWithAlias()
    {
        $this->engine->setSearchWithAlias();
        return $this;
    }

    //-------------PRIVATE FUNCTIONS-------------------

    /**
     * @param $value
     */
    private function handleiDisplayStart($value)
    {
        //skip
        $this->engine->skip($value);
    }

    /**
     * @param $value
     */
    private function handleiDisplayLength($value)
    {
        //limit nicht am query, sondern den ganzen
        //holen und dann dynamisch in der Collection taken und skippen
        $this->engine->take($value);
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
        $this->engine->search($value);
    }

    /**
     * @param $value
     */
    private function handleiSortCol_0($value)
    {
        if(Input::get('sSortDir_0') == 'desc')
            $direction = EngineInterface::ORDER_DESC;
        else
            $direction = EngineInterface::ORDER_ASC;

        //check if order is allowed
        if(empty($this->orderColumns))
        {
           $this->engine->order($value, $direction);
           return;
        }

        $i = 0;
        foreach($this->columns as $name => $column)
        {
            if($i == $value && in_array($name, $this->orderColumns))
            {
                $this->engine->order($value, $direction);
                return;
            }
            $i++;
        }
    }

    /**
     *
     */
    private function handleInputs()
    {
        //Handle all inputs magically
        foreach (Input::all() as $key => $input) {
            if(method_exists($this, $function = 'handle'.$key))
                $this->$function($input);
        }
    }

    private function prepareSearchColumns()
    {
        if(count($this->searchColumns) == 0 || empty($this->searchColumns))
            $this->searchColumns = $this->showColumns;
    }
}