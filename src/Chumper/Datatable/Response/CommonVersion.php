<?php


namespace Chumper\Datatable\Response;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class CommonVersion
 *
 * Code that is common to version 1.9 & 1.10 of DT
 *
 * @package Chumper\Datatable\Response
 */
class CommonVersion
{

    public $echo_value;

    /**
     * @var ParameterBag
     */
    protected $input;

    /**
     * @param ParameterBag $input
     */
    public function set_input(ParameterBag $input)
    {
        $this->input = $input;
    }

    public function set_echo_value($value)
    {
        $this->echo_value = intval($value);
    }

    public function get_bool($value)
    {
        return strtolower($value) === 'true' ? true : false;
    }

    public function get_direction($value)
    {
        $dir = trim(strtolower($value));

        if ($dir === 'asc' or $dir === 'desc')
            return $dir;

        return 'asc';
    }
}