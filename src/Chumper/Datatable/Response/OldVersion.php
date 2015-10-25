<?php


namespace Chumper\Datatable\Response;

/**
 * Class OldVersion
 *
 * Parse Inputs and generate responses for the old version (=< 1.9)
 * of Datatables.
 *
 * @package Chumper\Datatable\Response
 */
class OldVersion extends CommonVersion implements VersionResponse
{
    protected $start = 0;
    protected $length = 10;

    protected $is_searching = false;
    protected $search_is_regex = false;

    protected $number_columns = 0;
    protected $columns = [];
    protected $order = [];

    public function parse_request()
    {
        $this->start = intval($this->input->get('start', 0));
        $this->length = intval($this->input->get('length', 10));

        if ($search = $this->input->get('search')) {
            $this->parse_search_array($search);
        }

        if ($columns = $this->input->get('columns')) {
            $this->parse_columns($columns);
        }

        if ($order = $this->input->get('order')) {
            $this->parse_order($order);
        }
    }

    private function parse_search_array(array $search)
    {
        if (is_array($search)) {
            if (isset($search['value'])) {
                $this->search_is_regex = $this->get_bool($search['regex']);
                $this->is_searching = $search['value'];
            }
        }
    }

    private function parse_columns($columns)
    {
        $new_columns = array();

        if (count($columns) < 1)
            return false;

        $this->number_columns = count($columns);

        foreach($columns as $id => $column) {
            $_column = array();
            if (isset($column['name']))
                $_column['name'] = $column['name'];

            if (isset($columns['data']))
                $_column['data'] = $column['data'];

            if (isset($column['searchable']))
                $_column['searchable'] = $this->get_bool($column['searchable']);

            if (isset($column['orderable']))
                $_column['orderable'] = $this->get_bool($column['orderable']);

            if (isset($column['sortable']))
                $_column['sortable'] = $this->get_bool($column['sortable']);

            if (isset($column['search']) && is_array($column['search'])) {
                if (isset($column['search']['value'])) {
                    $_column['search_value'] = $column['search']['value'];
                    $_column['search_regex'] = $this->get_bool($column['search']['regex']);
                }
            }

            $new_columns[] = $_column;
        }

        $this->columns = $new_columns;
    }

    private function parse_order($order)
    {
        $new_order = array();

        if (!is_array($order) or count($order) === 0)
            return false;

        foreach($order as $column) {
            if (!isset($column['column']) or !isset($column['dir']))
                continue;

            $_order = array();
            $_order['column'] = intval($column['column']);

            if ($_order['column'] > $this->number_columns)
                continue;

            $_order['dir'] = $this->get_direction($column['dir']);

            $new_order[] = $_order;
        }

        $this->order = $new_order;
    }

    public function has_search_string()
    {
        return $this->is_searching;
    }
}