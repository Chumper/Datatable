<?php

namespace Chumper\Datatable\Columns;

/**
 * Class ColumnConfiguration
 * @package Chumper\Datatable\Columns
 *
 * The ColumnConfiguration is used to describe a column on the datatable. It contains all possible configuration options
 * so the data can be evaluated as well as the views can create a javascript representation of this configuration.
 */
class ColumnConfiguration
{

    /**
     * @var string The internal name of the column configuration
     */
    private $name;

    /**
     * @var string The label of the column used on the frontend
     */
    private $label;

    /**
     * @var bool Determines if the column can be searched on or not
     */
    private $searchable;

    /**
     * @var bool Determines if the column can be ordered on or not
     */
    private $orderable;

    /**
     * @var callable The function the user defines that should be called when the value of the columns should be calculated
     */
    private $callable;

    /**
     * ColumnConfiguration constructor.
     * As the class is immutable, all properties have to be set here
     *
     * @param string $name The internal name of the column configuration
     * @param string $label The label of the column shown in the frontend
     * @param callable $callable the function to call when the value should be calculated
     * @param bool $isSearchable If the column should be searchable
     * @param bool $isOrderable If the column should be orderable
     */
    public function __construct($name, $label, $callable, $isSearchable, $isOrderable)
    {
        $this->name = $name;
        $this->label = $label;
        $this->callable = $callable;
        $this->searchable = $isSearchable;
        $this->orderable = $isOrderable;
    }

    /**
     * Will return if the column is searchable
     *
     * @return boolean
     */
    public function isSearchable()
    {
        return $this->searchable;
    }

    /**
     * Will return if the column is orderable
     *
     * @return boolean
     */
    public function isOrderable()
    {
        return $this->orderable;
    }

    /**
     * Will return the internal name of this column configuration
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Will return the label of this column that will be shown on the frontend
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Will return the function that will be executed upon calculation.
     *
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }



}