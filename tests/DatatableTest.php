<?php

use Chumper\Datatable\Datatable;
use Illuminate\Support\Collection;

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
        $api = $this->dt->query($this->mock);

        $this->assertInstanceOf('Chumper\Datatable\Engines\QueryEngine', $api);

        $api = $this->dt->collection(new Collection());

        $this->assertInstanceOf('Chumper\Datatable\Engines\CollectionEngine', $api);

        $table = $this->dt->table();

        $this->assertInstanceOf('Chumper\Datatable\Table', $table);
    }

}
