<?php

use Chumper\Datatable\Table;
use Illuminate\Support\Facades\Request;

class TableTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Table
     */
    private $table;

    protected function setUp()
    {
        parent::setUp();

        Config::shouldReceive('get')->zeroOrMoreTimes()->with("datatable::table")->andReturn(
            [
                'class' => 'table table-bordered',
                'id' => '',
                'options' => [
                    "sPaginationType" => "full_numbers",
                    "bProcessing" => false
                ],
                'callbacks' => [],
                'noScript' => false,
                'table_view' => 'datatable::template',
                'script_view' => 'datatable::javascript',
            ]
        );

        $this->table = new Table();
    }

    /**
     * @expectedException Exception
     */
    public function testSetOptions()
    {
        $this->table->setOptions('foo', 'bar');

        $this->table->setOptions([
            'foo2' => 'bar2',
            'foo3' => 'bar3'
        ]);

        $this->table->setOptions('foo', 'bar', 'baz');
    }

    /**
     * @expectedException Exception
     */
    public function testSetCallbacks()
    {
        $this->table->setCallbacks('foo', 'bar');
        $this->assertArrayHasKey('foo', $this->table->getCallbacks());

        $this->table->setCallbacks([
            'foo2' => 'bar2',
            'foo3' => 'bar3'
        ]);
        $this->assertArrayHasKey('foo2', $this->table->getCallbacks());
        $this->assertArrayHasKey('foo3', $this->table->getCallbacks());

        $this->table->setCallbacks('foo', 'bar', 'baz');
        $this->assertTrue(false);  // should throw exception before here
    }

    /**
     * @expectedException Exception
     */
    public function testSetCustomValues()
    {
        $this->table->setCustomValues('foo', 'bar');
        $this->assertArrayHasKey('foo', $this->table->getCustomValues());

        $this->table->setCustomValues([
            'foo2' => 'bar2',
            'foo3' => 'bar3'
        ]);
        $this->assertArrayHasKey('foo2', $this->table->getCustomValues());
        $this->assertArrayHasKey('foo3', $this->table->getCustomValues());

        $this->table->setCustomValues('foo', 'bar', 'baz');
        $this->assertTrue(false);  // should throw exception before here
    }

    public function testAddColumn()
    {
        $this->table->addColumn('foo');

        $this->assertEquals(1, $this->table->countColumns());

        $this->table->addColumn('foo1', 'foo2');

        $this->assertEquals(3, $this->table->countColumns());

        $this->table->addColumn(['foo3','foo4']);

        $this->assertEquals(5, $this->table->countColumns());
    }

    public function testRender()
    {
        Request::shouldReceive('url')->once()->andReturn('fooBar');

        View::shouldReceive('make')->once()
            ->with('datatable::template', [
                'options'   => [
                    'sAjaxSource' => 'fooBar',
                    'bServerSide' => true,
                    'sPaginationType'=>'full_numbers',
                    'bProcessing'=>false
                ],
                'callbacks' => [],
                'values'    => [],
                'data'      => [],
                'columns'   => [1=>'foo'],
                'noScript'  => false,
                'class'     => $this->table->getClass(),
                'id'        => $this->table->getId(),

            ])->andReturn(true);

        $table1 = $this->table->addColumn('foo')->render();

        $this->assertTrue($table1);
    }

    public function testSetData()
    {
        $data = [
            [
                'foo',
                'bar'
            ],
            [
                'foo2',
                'bar2'
            ],
        ];

        $this->table->setData($data);
        $this->assertEquals($data, $this->table->getData());
    }

    public function testSetUrl()
    {
        $this->table->setUrl('foo/url');

        $this->assertArrayHasKey('bServerSide', $this->table->getOptions());
        $this->assertArrayHasKey('sAjaxSource', $this->table->getOptions());

        $return = $this->table->getOptions();

        $this->assertEquals('foo/url', $return['sAjaxSource']);
    }

    protected function tearDown()
    {
        Mockery::close();
    }
}
