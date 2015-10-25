<?php

namespace Chumper\Datatable\Providers;
use Chumper\Datatable\Interfaces\ColumnConfiguration;
use Chumper\Datatable\Interfaces\PresentationConfiguration;

/**
 * Interface DatatableProvider
 * @package Chumper\Datatable\Providers
 *
 * Base interface for all datatable providers. A datatable provider will proccess the underlaying data based on the
 *given configuration that it will get.
 */
interface DTProvider
{

    /**
     * This method should process all configurations and prepare the underlying data for the view. It will arrange the
     * data and provide the results in a DTData object.
     *
     * @param ColumnConfiguration $columnConfiguration
     * @param PresentationConfiguration $presentationConfiguration
     * @return mixed
     */
    public function process(ColumnConfiguration $columnConfiguration, PresentationConfiguration $presentationConfiguration);
}