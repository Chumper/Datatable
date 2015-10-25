<?php

namespace Chumper\Datatable\Composers;

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

    private function modelColumn($name)
    {
        return $this;
    }

    private function functionColumn($name, callable $function)
    {
        return $this;
    }

    private function labelColumn($name, $label)
    {
        return $this;
    }

    private function labelFunctionColumn($name, $label, callable $function)
    {
        return $this;
    }

    public function __call($name, $arguments)
    {
        if ($name === 'column'){
            if(count($arguments) === 1 ){
                return $this->modelColumn($arguments[0]);
            }
            if(count($arguments) === 2){
                if(is_callable($arguments[1])) {
                    return $this->functionColumn($arguments[0], $arguments[1]);
                } else {
                    return $this->labelColumn($arguments[0], $arguments[1]);
                }
            }
            if(count($arguments) === 3){
                return $this->labelFunctionColumn($arguments[0], $arguments[1], $arguments[2]);
            }
        }
        throw new \InvalidArgumentException("Method not supported");
    }


}