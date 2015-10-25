<?php

namespace Chumper\Datatable\Columns;


/**
 * Class ColumnConfigurationBuilder
 * @package Chumper\Datatable\Columns
 *
 * Simple builder class for the column configuration
 */
class ColumnConfigurationBuilder
{

    /**
     * @var string
     */
    private $name = null;

    /**
     * @var string
     */
    private $label = null;

    /**
     * @var callable
     */
    private $callable = null;

    /**
     * @var bool
     */
    private $searchable = true;

    /**
     * @var bool
     */
    private $orderable = true;

    /**
     * ColumnConfigurationBuilder constructor.
     */
    private function __construct()
    {
    }

    /**
     * Will create a new builder for a ColumnConfigurationBuilder.
     *
     * @return ColumnConfigurationBuilder
     */
    public static function create()
    {
        return new ColumnConfigurationBuilder();
    }

    /**
     * @param string $name
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function label($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param bool $searchable
     * @return $this
     */
    public function searchable($searchable)
    {
        $this->searchable = $searchable;
        return $this;
    }

    /**
     * @param bool $orderable
     * @return $this
     */
    public function orderable($orderable)
    {
        $this->orderable = $orderable;
        return $this;
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function withCallable($callable)
    {
        $this->callable = $callable;
        return $this;
    }

    /**
     * Will create the final ColumnConfiguration
     *
     * @return ColumnConfiguration
     */
    public function build()
    {
        $this->checkName();
        $this->checkLabel();
        $this->checkCallable();

        return new ColumnConfiguration($this->name, $this->label, $this->callable, $this->searchable, $this->orderable);
    }

    /**
     * Will check if the name is empty and throws an exception if that is the case.
     */
    private function checkName()
    {
        if(empty($this->name))
        {
            throw new \InvalidArgumentException("The name can not be empty");
        }
    }

    /**
     * Will check if the label is set. If not it will set the label to the given name.
     */
    private function checkLabel()
    {
        if(empty($this->label))
        {
            $this->label($this->name);
        }
    }

    /**
     * Will check if the callable is set and is executable, if not a sensible default will be set.
     */
    private function checkCallable()
    {
        if(is_null($this->callable) || !is_callable($this->callable))
        {
            $self = $this;
            $this->callable = function($data) use ($self) {
                $name = $self->name;

                if(is_array($data) && array_key_exists($name, $data))
                {
                    return $data[$name];
                } else if(is_object($data) && property_exists($data, $name))
                {
                    return $data->$name;
                } else if(is_object($data) && method_exists($data, $name))
                {
                    return $data->$name();
                } else {
                    return "";
                }
            };
        }
    }

}