<?php namespace Chumper\Datatable;

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

    //TODO create new functions where to search on

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
        if(func_num_args() != 2)
            throw new Exception('Invalid number of arguments');

        if(is_callable(func_get_arg(1)))
        {
            $this->columns->put(func_get_arg(0), new FunctionColumn(func_get_arg(1)));
        }
        else
        {
            $this->columns->put(func_get_arg(0), new TextColumn(func_get_arg(0)));
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
     * @return $this
     */
    public function clearColumns()
    {
        $this->columns = new Collection();
        return $this;
    }

    /**
     * @return $this
     */
    public function showColumns($cols)
    {
        if ( ! is_array($cols)) {
            $cols = func_get_args();
        }

        foreach ($cols as $property) {
            $this->columns->put($property, new FunctionColumn(function($model) use($property){return $model[$property];}));
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

        $output = array(
            "aaData" => $this->engine->make($this->columns)->toArray(),
            "sEcho" => intval($this->sEcho),
            "iTotalRecords" => $this->engine->totalCount(),
            "iTotalDisplayRecords" => $this->engine->count(),
        );

        return Response::json($output);
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

        $this->engine->order($value, $direction);
    }

    /**
     *
     */
    private function handleInputs()
    {
        foreach (Input::all() as $key => $input) {
            if(method_exists($this, $function = 'handle'.$key))
                $this->$function($input);
        }
    }
}