<?php

namespace Chumper\Datatable\Composers;


use Chumper\Datatable\Providers\DTProvider;
use Mockery as m;

class DTDataComposerTest extends \PHPUnit_Framework_TestCase
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
        m::close();
    }

    protected function setUp()
    {
        $this->provider = m::mock('Chumper\Datatable\Providers\DTProvider');
//        $this->provider = m::mock('Chumper\Datatable\Providers\DTProvider');

        $this->composer = new DTDataComposer($this->provider);
    }

    public function testGetProvider()
    {
        $this->assertTrue($this->provider != null);
        $this->assertSame($this->provider, $this->composer->getProvider());
    }
}
