<?php namespace Chumper\Datatable\Columns;

/**
 * Interface for the columns that will be used by the user
 *
 * Class ColumnInterface
 * @package Chumper\Datatable\Columns
 */
interface ColumnInterface {

    /**
     * @param mixed $model The data to pass to the column,
     *              could be a model or an array
     * @return mixed the return value of the implementation,
     *              should be text in most of the cases
     */
    public function run($model);

    /**
     * @param $name The name of the column
     * @return void
     */
    public function setName($name);

    /**
     * @return String The name of the column
     */
    public function getName();

}