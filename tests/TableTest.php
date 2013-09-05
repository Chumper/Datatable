<?php

use Chumper\Datatable\Table;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\View\Environment;

class TableTest extends TestCase {

    /**
     * @var Table
     */
    private $table;

    protected function setUp()
    {
        parent::setUp();
        $this->table = new Table();
        $test = Mockery::mock('View');
        $test->shouldReceive('make')->once();
    }

    /**
     * @expectedException Exception
     */
    public function testSetOptions()
    {
        $this->table->setOptions('foo','bar');

        $this->table->setOptions(array(
            'foo2' => 'bar2',
            'foo3' => 'bar3'
        ));

        $this->table->setOptions('foo', 'bar', 'baz');
    }

    public function testAddColumn()
    {
        $this->table->addColumn('foo');

        $this->assertEquals(1, $this->table->countColumns());

        $this->table->addColumn('foo1','foo2');

        $this->assertEquals(3, $this->table->countColumns());

        $this->table->addColumn(array('foo3','foo4'));

        $this->assertEquals(5, $this->table->countColumns());
    }

    public function testRender()
    {
        $table1 = $this->table->addColumn('foo')
            ->render();



        //$should = $this->getTable1Result();
    }

    public function testSetData()
    {
        $data = array(
            array(
                'foo',
                'bar'
            ),
            array(
                'foo2',
                'bar2'
            ),
        );

        $this->table->setData($data);
        $this->assertEquals($data,$this->table->getData());

    }

    public function testSetUrl()
    {
        $this->table->setUrl('foo/url');

        $this->assertArrayHasKey('bServerSide',$this->table->getOptions());
        $this->assertArrayHasKey('sAjaxSource',$this->table->getOptions());

        $return = $this->table->getOptions();

        $this->assertEquals('foo/url',$return['sAjaxSource']);
    }

}