<?php

use Chumper\Datatable\Columns\ColumnConfiguration;
use Chumper\Datatable\Composers\DTDataComposer;
use Chumper\Datatable\Providers\DTProvider;

class DTDataComposerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var DTDataComposer
     */
    protected $composer;

    /**
     * @var DTProvider
     */
    protected $provider;


    public function tearDown()
    {
        Mockery::close();
    }


    protected function setUp()
    {
        $this->provider = Mockery::mock('Chumper\Datatable\Providers\DTProvider');
        $this->composer = new DTDataComposer($this->provider);
    }

    /**
     * Test for setting and getting the provider
     */
    public function testGetProvider()
    {
        $this->assertTrue($this->provider != null);
        $this->assertTrue($this->composer != null);
        $this->assertSame($this->provider, $this->composer->getProvider());
    }

    /**
     * Will test if a normal column can be added and results in the correct ColumnConfiguration
     */
    public function testModelColumnConfiguration()
    {
        $name = "fooBar";

        $this->composer->modelColumn($name);

        // get configuration and verify
        $numberOfColumns = count($this->composer->getColumnConfiguration());
        $this->assertSame($numberOfColumns, 1, "There should only be one column configuration");

        /**
         * @var ColumnConfiguration
         */
        $cc = $this->composer->getColumnConfiguration()[0];

        $this->assertTrue($cc->isOrderable(), "The column should be orderable");
        $this->assertTrue($cc->isSearchable(), "The column should be searchable");
        $this->assertSame($name, $cc->getName(), "The name should be set to 'fooBar'");
        $this->assertSame($name, $cc->getLabel(), "The label should be set to the correct value");
    }

    /**
     * Will test if a normal column can be added and results in the correct ColumnConfiguration
     */
    public function testLabelColumnConfiguration()
    {
        $name = "fooBar";
        $label = "barFoo";

        $this->composer->labelColumn($name, $label);

        // get configuration and verify
        $numberOfColumns = count($this->composer->getColumnConfiguration());
        $this->assertSame($numberOfColumns, 1, "There should only be one column configuration");

        /**
         * @var ColumnConfiguration
         */
        $cc = $this->composer->getColumnConfiguration()[0];

        $this->assertTrue($cc->isOrderable(), "The column should be orderable");
        $this->assertTrue($cc->isSearchable(), "The column should be searchable");
        $this->assertSame($name, $cc->getName(), "The name should be set to 'fooBar'");
        $this->assertSame($label, $cc->getLabel(), "The label should be set to the correct value");
    }

    /**
     * Will test if a normal column can be added and results in the correct ColumnConfiguration
     */
    public function testFunctionColumnConfiguration()
    {
        $name = "fooBar";

        $this->composer->functionColumn($name, function($data) { return "fooBar"; });

        // get configuration and verify
        $numberOfColumns = count($this->composer->getColumnConfiguration());
        $this->assertSame($numberOfColumns, 1, "There should only be one column configuration");

        /**
         * @var ColumnConfiguration
         */
        $cc = $this->composer->getColumnConfiguration()[0];

        $this->assertTrue($cc->isOrderable(), "The column should be orderable");
        $this->assertTrue($cc->isSearchable(), "The column should be searchable");
        $this->assertSame($name, $cc->getName(), "The name should be set to 'fooBar'");
        $this->assertSame($name, $cc->getLabel(), "The label should be set to the correct value");

        $func = $cc->getCallable();

        $this->assertSame("fooBar", $func(["foo" => "bar"]));

        $this->assertSame("fooBar", $func(null));
    }

}
