<?php

use Chumper\Datatable\Engines\CollectionEngine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Config;

class BaseEngineTest extends TestCase
{

    private $collection;

    /**
     * @var CollectionEngine
     */
    private $engine;

    public function setUp()
    {
        // set up config
        Config::shouldReceive('get')->zeroOrMoreTimes()->with("datatable::engine")->andReturn(
            [
                'exactWordSearch' => false,
                ]
        );

        $this->collection = new Collection();
        $this->engine = new CollectionEngine($this->collection);
    }


    /**
     * @expectedException Exception
     */
    public function testAddColumn()
    {
        $this->engine->addColumn('foo', 'bar');

        $this->assertInstanceOf(
            'Chumper\Datatable\Columns\TextColumn',
            $this->engine->getColumn('foo')
        );

        $this->engine->addColumn('foo2', function ($model) {
            return $model->fooBar;
        });

        $this->assertInstanceOf(
            'Chumper\Datatable\Columns\FunctionColumn',
            $this->engine->getColumn('foo2')
        );

        $this->assertEquals([1 => 'foo2', 0 => 'foo'], $this->engine->getOrder());

        $this->engine->addColumn();
    }

    public function testClearColumns()
    {
        $this->engine->addColumn('foo', 'Bar');
        $this->assertInstanceOf(
            'Chumper\Datatable\Columns\TextColumn',
            $this->engine->getColumn('foo')
        );

        $this->engine->clearColumns();
        $this->assertEquals([], $this->engine->getOrder());
    }

    public function testSearchColumns()
    {
        $this->engine->searchColumns('id');

        $this->assertEquals(['id'], $this->engine->getSearchingColumns());

        $this->engine->searchColumns('name', 'email');

        $this->assertEquals(['name','email'], $this->engine->getSearchingColumns());

        $this->engine->searchColumns(['foo', 'bar']);

        $this->assertEquals(['foo', 'bar'], $this->engine->getSearchingColumns());
    }

    public function testOrderColumns()
    {
        $this->engine->orderColumns('id');

        $this->assertEquals(['id'], $this->engine->getOrderingColumns());

        $this->engine->orderColumns('name', 'email');

        $this->assertEquals(['name','email'], $this->engine->getOrderingColumns());

        $this->engine->orderColumns(['foo', 'bar']);

        $this->assertEquals(['foo', 'bar'], $this->engine->getOrderingColumns());
    }

    public function testShowColumns()
    {
        $this->engine->showColumns('id');

        $this->assertEquals(['id'], $this->engine->getOrder());

        $this->engine->showColumns('name', 'email');

        $this->assertEquals(['id','name','email'], $this->engine->getOrder());

        $this->engine->showColumns(['foo', 'bar']);

        $this->assertEquals(['id','name','email', 'foo', 'bar'], $this->engine->getOrder());
    }
}
