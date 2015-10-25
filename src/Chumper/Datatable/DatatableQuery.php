<?php


namespace Chumper\Datatable;


class DatatableQuery
{

    /** @var bool do we need to search on columns, or just order & filter? */
    public $searchColumns = false;

    /** @var bool are we using a plugin to search individual plugins */
    public $searchIndividualColumns = true;

    /** @var string The string we are searching for (note: for searchColumns) */
    public $searchString = '';

    /** @var array the columns that we are searching, the content that has been put in */
    public $searchColumn = [];

    /** @var bool the search is a regular expression */
    public $searchRegex = true;

    /** @var int the number of columns we are showing in the datatable */
    public $numberOfColumns = 0;

    /** @var array a list of all the columns we are showing */
    public $columns = [];

    /** @var array a list of the columns we are sorting by, with their direction */
    /* [    [ 'id' => 'desc' ], [ 'name', 'asc' ]    ] */
    public $order = [];

    /** @var int which result to start from */
    public $start = 0;

    /** @var int the limit of the result. */
    public $limit = 0;
}

