<?php

use Chumper\Datatable\Columns\FunctionColumn;
use Chumper\Datatable\Columns\TextColumn;
use Chumper\Datatable\Engines\CollectionEngine;
use Chumper\Datatable\Engines\EngineInterface;
use Illuminate\Support\Collection;

class CollectionEngineTest extends PHPUnit_Framework_TestCase {

    /**
     * @var CollectionEngine
     */
    public $c;

    /**
     * @var \Mockery\Mock
     */
    public $collection;

    public function setUp()
    {
        $this->collection = Mockery::mock('Illuminate\Support\Collection');
        $this->c = new CollectionEngine($this->collection);
    }

    public function testOrder()
    {
        $should = array(
            array(
                'id' => 'eoo'
            ),
            array(
                'id' => 'foo'
            )
        );

        $engine = new CollectionEngine(new Collection($this->getTestArray()));

        $engine->order('id');

        $this->assertEquals($should, $engine->getArray());

        $should2 = array(
            array(
                'id' => 'foo'
            ),
            array(
                'id' => 'eoo'
            )
        );

        $engine->order('id', EngineInterface::ORDER_DESC);

        $this->assertEquals($should2, $engine->getArray());

    }

    public function testSearch()
    {
        $engine = new CollectionEngine(new Collection($this->getTestArray()));

        $engine->search('eoo');

        $should = array(
            array(
                'eoo'
            )
        );

        $this->assertEquals($should, $engine->make($this->getTestColumns(), array('id'))->toArray());

        //------------------TEST 2-----------------
        // search in outputed data
        $engine = new CollectionEngine(new Collection(array(array('foo', 'foo2', 'foo3'),array('bar', 'bar2', 'bar3'))));

        $engine->search('foo2');

        $should = array(
            array(
                'foo - foo2',
                'foo3',
                'foo - foo3'
            )
        );

        $this->assertEquals($should, $engine->make(new Collection(array(
            new FunctionColumn('bla', function($row){return $row[0]." - ".$row[1];}),
            new FunctionColumn('1', function($row){return $row[2];}),
            new FunctionColumn('bla3', function($row){return $row[0]." - ".$row[2];})
        )), array('bla', 1))->toArray());

        //------------------TEST 3-----------------
        // search in initial data
        // TODO: Search in initial data columns?

        $engine = new CollectionEngine(new Collection(array(array('foo', 'foo2', 'foo3'),array('bar', 'bar2', 'bar3'))));

        $engine->search('foo2');

        $should = array(
            array(
                'foo - foo3',
                'foo2'
            )
        );

        $this->assertEquals($should, $engine->make(new Collection(array(
            new FunctionColumn('bla3', function($row){return $row[0]." - ".$row[2];}),
            new FunctionColumn('1', function($row){return $row[1];})
        )), array('bla3', 1))->toArray());
    }

    public function testSkip()
    {
        $engine = new CollectionEngine(new Collection($this->getTestArray()));

        $engine->skip(1);

        $should = array(
            array(
                'id' => 'eoo',
            )
        );
        $this->assertEquals($should, $engine->getArray());
    }

    public function testTake()
    {
        $engine = new CollectionEngine(new Collection($this->getTestArray()));

        $engine->take(1);

        $should = array(
            array(
                'id' => 'foo',
            )
        );
        $this->assertEquals($should, $engine->getArray());
    }

    public function testComplex()
    {
        $engine = new CollectionEngine(new Collection($this->getRealArray()));

        $engine->search('t');
        $test = $engine->make(new Collection($this->getRealColumns()),array('foo','bar'))->toArray();

        $this->assertTrue($this->arrayHasKeyValue('0','Nils',$test));
        $this->assertTrue($this->arrayHasKeyValue('0','Taylor',$test));

        //Test2
        $engine = new CollectionEngine(new Collection($this->getRealArray()));

        $engine->search('plasch');
        $test = $engine->make(new Collection($this->getRealColumns()),array('foo','bar'))->toArray();

        $this->assertTrue($this->arrayHasKeyValue('0','Nils',$test));

        //test3
        $engine = new CollectionEngine(new Collection($this->getRealArray()));

        $engine->search('tay');
        $test = $engine->make(new Collection($this->getRealColumns()),array('foo','bar'))->toArray();

        $this->assertTrue($this->arrayHasKeyValue('0','Taylor',$test));

        //test4
        $engine = new CollectionEngine(new Collection($this->getRealArray()));

        $engine->search('0');
        $test = $engine->make(new Collection($this->getRealColumns()),array('foo','bar'))->toArray();

        $this->assertTrue($this->arrayHasKeyValue('0','Taylor',$test));

    }

    protected function tearDown()
    {
        Mockery::close();
    }

    private function getTestArray()
    {
        return array(
            array(
                'id' => 'foo'
            ),
            array(
                'id' => 'eoo'
            )
        );
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
            new FunctionColumn('foo', function($m){return $m['name'];}),
            new FunctionColumn('bar', function($m){return $m['email'];}),
        );
    }

    private function getTestColumns()
    {
        return new Collection(
            array(
                new FunctionColumn('id', function($row){return $row['id'];}),
            )
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
