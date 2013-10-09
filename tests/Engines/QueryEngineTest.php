<?php

use Chumper\Datatable\Columns\FunctionColumn;
use Chumper\Datatable\Engines\EngineInterface;
use Chumper\Datatable\Engines\QueryEngine;
use Illuminate\Support\Collection;

class QueryEngineTest extends PHPUnit_Framework_TestCase {

    /**
     * @var QueryEngine
     */
    public $c;

    /**
     * @var \Mockery\Mock
     */
    public $builder;

    public function setUp()
    {
        $this->builder = Mockery::mock('Illuminate\Database\Query\Builder');
        $this->c = new QueryEngine($this->builder);
    }

    public function testOrder()
    {
        $this->builder->shouldReceive('orderBy')->with('id', EngineInterface::ORDER_ASC);
        $this->c->order('id');

        //--

        $this->builder->shouldReceive('orderBy')->with('id', EngineInterface::ORDER_DESC);
        $this->c->order('id', EngineInterface::ORDER_DESC);
    }

    public function testSearch()
    {
        $this->builder->shouldReceive('orWhere')->with('foo','like','test');
        $this->builder->shouldReceive('get')->once();

        $this->c->search('test');
        $collection = $this->c->make(array('foo'));

        //

        $this->builder->shouldReceive('orWhere')->once()->with('foo','like','test');
        $this->builder->shouldReceive('get')->once();

        $this->c->search('test');
        $collection = $this->c->make(array('foo','bar'),array('foo'));

    }

    public function testSkip()
    {
        $this->builder->shouldReceive('skip')->once()->with(10);

        $this->c->skip(10);
    }

    public function testTake()
    {
        $this->builder->shouldReceive('take')->once()->with(10);

        $this->c->take(10);
    }

    public function testComplex()
    {
        $engine = new QueryEngine($this->builder);

        $engine->search('t');
        $test = $engine->make($this->getRealColumns())->toArray();

        $this->assertTrue($this->arrayHasKeyValue('0','Nils',$test));
        $this->assertTrue($this->arrayHasKeyValue('0','Taylor',$test));

        //Test2
        $engine = new QueryEngine(new Collection($this->getRealArray()));

        $engine->search('plasch');
        $test = $engine->make($this->getRealColumns())->toArray();

        $this->assertTrue($this->arrayHasKeyValue('0','Nils',$test));

        //test3
        $engine = new QueryEngine(new Collection($this->getRealArray()));

        $engine->search('tay');
        $test = $engine->make($this->getRealColumns())->toArray();

        $this->assertTrue($this->arrayHasKeyValue('0','Taylor',$test));

        //test4
        $engine = new QueryEngine(new Collection($this->getRealArray()));

        $engine->search('0');
        $test = $engine->make($this->getRealColumns())->toArray();

        $this->assertTrue($this->arrayHasKeyValue('0','Taylor',$test));



    }

    protected function tearDown()
    {
        Mockery::close();
    }

    private function getRealArray()
    {
        return array(
            array(
                'name' => 'Nils Plaschke',
                'email'=> 'github@nilsplaschke.de'
            ),
            array(
                'name' => 'Taylor Otwell',
                'email'=> 'taylorotwell@gmail.com'
            )
        );
    }

    private function getRealColumns()
    {
        return array(
            new FunctionColumn(function($m){return $m['name'];}),
            new FunctionColumn(function($m){return $m['email'];}),
        );
    }

    private function arrayHasKeyValue($key,$value,$array)
    {
        $array = array_pluck($array,$key);
        foreach ($array as $val)
        {
            if(str_contains($val, $value))
                return true;
        }
        return false;

    }

}
