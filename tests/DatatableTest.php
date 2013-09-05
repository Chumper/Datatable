<?php

use Chumper\Datatable\Datatable;

class DatatableTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Datatable
     */
    private $dt;

    protected function setUp()
    {
        $this->dt = new Datatable;
        $this->mock = Mockery::mock('Illuminate\Database\Query\Builder');
    }

    public function testReturnInstances()
    {
        $api = $this->dt->api($this->mock);

        $this->assertInstanceOf('Chumper\Datatable\Api', $api);

        $table = $this->dt->table();

        $this->assertInstanceOf('Chumper\Datatable\Table', $table);
    }

}
