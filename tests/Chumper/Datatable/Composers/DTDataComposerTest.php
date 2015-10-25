<?php

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

    public function testGetProvider()
    {
        $this->assertTrue($this->provider != null);
        $this->assertTrue($this->composer != null);
        $this->assertSame($this->provider, $this->composer->getProvider());
    }
}
