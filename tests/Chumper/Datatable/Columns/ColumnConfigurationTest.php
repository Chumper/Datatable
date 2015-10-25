<?php

use Chumper\Datatable\Columns\ColumnConfigurationBuilder;

class ColumnConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Simple test that will test if the immutable object is created as wished
     */
    public function testBasicColumnConfiguration()
    {
        $name = "fooBar";

        $cc = ColumnConfigurationBuilder::create()
            ->name($name)
            ->searchable(false)
            ->orderable(false)
            ->build();

        $this->assertSame($name, $cc->getName(), "Name should be set correctly");
        $this->assertFalse($cc->isSearchable(), "The column should be searchable");
        $this->assertFalse($cc->isOrderable(), "The column should be orderable");
    }

    /**
     * Will test the label
     */
    public function testLabel()
    {
        $cc = ColumnConfigurationBuilder::create()
            ->name("fooBar")
            ->label("barFoo")
            ->build();

        $this->assertSame("fooBar", $cc->getName());
        $this->assertSame("barFoo", $cc->getLabel());
    }

    /**
     * Will test if the builder will throw an exception on an empty name
     * @expectedException InvalidArgumentException
     */
    public function testInvalidConfiguration()
    {
        ColumnConfigurationBuilder::create()
            ->name("")
            ->build();
    }

    /**
     * Will test if the builder will set the label correctly when it is missing
     */
    public function testMissingLabel()
    {
        $name = "fooBar";
        $cc = ColumnConfigurationBuilder::create()
            ->name($name)
            ->searchable(true)
            ->orderable(true)
            ->build();

        $this->assertSame($name, $cc->getName(), "The name should be set to the correct value");
        $this->assertSame($name, $cc->getLabel(), "The name should be set to the correct value");
        $this->assertTrue($cc->isSearchable(), "The column should be searchable");
        $this->assertTrue($cc->isOrderable(), "The column should be orderable");
    }

    public function testCallable()
    {
        $obj = new FooClass();

        $cc = ColumnConfigurationBuilder::create()
            ->name("fooBar")
            ->build();
        $func = $cc->getCallable();

        $this->assertSame("", $func(["foo" => "bar"]));

        $this->assertSame("bar", $func(["fooBar" => "bar"]));

        $this->assertTrue(is_object($obj));

        $this->assertSame("", $func($obj));

        $cc = ColumnConfigurationBuilder::create()
            ->name("fooProperty")
            ->build();
        $func = $cc->getCallable();

        $this->assertSame("barProperty", $func($obj));

        $cc = ColumnConfigurationBuilder::create()
            ->name("fooMethod")
            ->build();
        $func = $cc->getCallable();

        $this->assertSame("barMethod", $func($obj));

    }


}
