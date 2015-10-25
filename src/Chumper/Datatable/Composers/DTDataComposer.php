<?php

namespace Chumper\Datatable\Composers;

use Chumper\Datatable\Columns\ColumnConfiguration;
use Chumper\Datatable\Columns\ColumnConfigurationBuilder;
use Chumper\Datatable\Providers\DTProvider;

/**
 * Class DTDataComposer
 * @package Chumper\Datatable\Composers
 *
 * The composer is responsible to collect all column configuration as well as view configurations and to pass them
 * to the DTProvider when the data needs to be collected.
 */
class DTDataComposer
{
    /**
     * @var DTProvider The Provider for the underlying data.
     */
    private $provider;

    /**
     * @var ColumnConfiguration[] An array of the configurations of the columns
     */
    private $columnConfiguration = [];

    /**
     * Will create a new datatable composer instance with the given provider
     * @param DTProvider $provider the provider that will process the underlying data
     */
    public function __construct(DTProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Will return the Provider for the underlying data.
     * @return DTProvider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Most basic column configuration. The resulting configuration will try to return the given name property of the
     * data passed to it. It will default to be searchable and orderable.
     *
     * @param string $name The name of the property to show
     * @return $this
     */
    public function modelColumn($name)
    {
        $this->columnConfiguration[] = ColumnConfigurationBuilder::create()
            ->name($name)
            ->build();

        return $this;
    }

    /**
     * Will create a column configuration with the given name and label and default settings.
     *
     * @param string $name
     * @param string $label
     * @return $this
     */
    public function labelColumn($name, $label)
    {
        $this->columnConfiguration[] = ColumnConfigurationBuilder::create()
            ->name($name)
            ->label($label)
            ->build();
        return $this;
    }

    /**
     * Will create a column configuration with the given name and the given callable
     *
     * @param string $name The internal name of this column configuration
     * @param callable $function the callable to execute
     * @return $this
     */
    public function functionColumn($name, callable $function)
    {
        $this->columnConfiguration[] = ColumnConfigurationBuilder::create()
            ->name($name)
            ->withCallable($function)
            ->build();

        return $this;
    }

    /**
     * Will return the internal column configurations that are registered with the current composer.
     *
     * @return ColumnConfiguration[]
     */
    public function getColumnConfiguration()
    {
        return $this->columnConfiguration;
    }





    public function labelFunctionColumn($name, $label, callable $function)
    {
        return $this;
    }
}