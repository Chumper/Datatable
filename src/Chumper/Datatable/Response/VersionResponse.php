<?php


namespace Chumper\Datatable\Response;


use Symfony\Component\HttpFoundation\ParameterBag;

interface VersionResponse
{
    public function set_input(ParameterBag $input);

    public function set_echo_value($value);

    /** @return bool */
    public function has_search_string();
}