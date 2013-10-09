<?php

use Chumper\Datatable\Api;
use Illuminate\Support\Collection;

class ApiTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Api
     */
    private $api;

    /**
     * @var \Mockery\Mock
     */
    private $input;

    /**
     * @var \Mockery\Mock
     */
    private $query;

    /**
     * @var \Mockery\Mock
     */
    private $response;

    protected function setUp()
    {
        parent::setUp();

        /*Datatable::api($users)
            ->addColumn('test','bla')
            ->addColumn('test2',function($model){
                return $model->getPresenter()->fullName;
            })
            ->showColumns('id','name')
            ->searchOn('id','post.user.name')
            ->hideColumns()
            ->make();
        */
        $this->engine = Mockery::mock('Chumper\Datatable\Engines\CollectionEngine');
        $this->api = new Api($this->engine);

        //View Mock
        $app = Mockery::mock('AppMock');
        $app->shouldReceive('instance')->once()->andReturn($app);

        Illuminate\Support\Facades\Facade::setFacadeApplication($app);

        $this->input = Mockery::mock('InputMock');
        Illuminate\Support\Facades\Input::swap($this->input);

        //$this->response = Mockery::mock('Illuminate\Support\Facades\Response');
        //Illuminate\Support\Facades\Response::swap($this->response);
    }

    /**
     * @expectedException Exception
     */
    public function testAddColumn()
    {
        $this->api->addColumn('foo', 'bar');

        $this->assertInstanceOf(
            'Chumper\Datatable\Columns\TextColumn',
            $this->api->getColumn('foo')
        );

        $this->api->addColumn('foo2', function($model){return $model->fooBar;});

        $this->assertInstanceOf(
            'Chumper\Datatable\Columns\FunctionColumn',
            $this->api->getColumn('foo2')
        );

        $this->assertEquals(array(1 => 'foo2', 0 => 'foo'), $this->api->getOrder());

        $this->api->addColumn();
    }

    public function testClearColumns()
    {
        $this->api->addColumn('foo','Bar');
        $this->assertInstanceOf(
            'Chumper\Datatable\Columns\TextColumn',
            $this->api->getColumn('foo')
        );

        $this->api->clearColumns();
        $this->assertEquals(array(), $this->api->getOrder());
    }

    public function testShowColumns()
    {
        $this->api->showColumns('id');

        $this->assertEquals(array('id'), $this->api->getOrder());

        $this->api->showColumns('name', 'email');

        $this->assertEquals(array('id','name','email'), $this->api->getOrder());

        $this->api->showColumns(array('foo', 'bar'));

        $this->assertEquals(array('id','name','email', 'foo', 'bar'), $this->api->getOrder());
    }

    public function make()
    {

        $this->markTestSkipped('not ready yet');
        $this->engine->shouldReceive('get')->once()
            ->andReturn(new Collection(array(array('foo' => 'foo'))));
        $this->engine->shouldReceive('count')->once()->andReturn(1);
        $this->engine->shouldReceive('totalCount')->once()->andReturn(1);

        $this->input->shouldReceive('all')->once()
            ->andReturn(array(
                'iDisplayStart'     =>  0,
                'iDisplayLength'    =>  10,
                'sEcho'             =>  1,
            ));

        $result = $this->api
            ->showColumns('foo')
            ->addColumn('blub',function($model){return $model->foo.'Bar';})
            ->make();

        $should = '{"sEcho":1,"iTotalRecords":1,"iTotalDisplayRecords":1,"aaData":[{"foo","fooBar"}]}';

        $this->assertEquals($should,$result->getContent());
    }

    protected function tearDown()
    {
        Mockery::close();
    }
}
