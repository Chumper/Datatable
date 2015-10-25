<?php


namespace Chumper\Datatable;

use Chumper\Datatable\Response\VersionResponse;
use Illuminate\Http\Request;
use Chumper\Datatable\DatatableVersion as Version;


class InputEngine
{

    public $should_handle = false;

    /* @var \Symfony\Component\HttpFoundation\ParameterBag */
    protected $input;
    protected $version;

    protected $echo_value;

    /** @var VersionResponse */
    protected $response;

    public function __construct(Request $request)
    {
        $this->input = $request->query;

        $echo_value_old = $this->input->get('sEcho', null);
        $echo_value_new = $this->input->get('draw', null);

        if (is_null($echo_value_old) && is_null($echo_value_new)) {
            $this->should_handle = false;
        } else {
            $this->should_handle = true;

            // Don't handle the request if we are meant to serve responses for both v1.9 && v1.10.
            if (!is_null($echo_value_old) && !is_null($echo_value_new)) {
                $this->should_handle = false;
                return;
            }

            if (!is_null($echo_value_old)) {
                // Old (1.9) datatables
                $this->version = Version::OLD_VERSION;
                $this->echo_value = $echo_value_old;

                $this->response = new Response\OldVersion();
                $this->response->set_input($input);
                $this->response->set_echo_value($this->echo_value);
            } elseif (!is_null($echo_value_new)) {
                // New (1.10) datatables
                $this->version = Version::NEW_VERSION;
                $this->echo_value = $echo_value_new;

                $this->response = new Response\NewVersion();
                $this->response->set_input($this->input);
                $this->response->set_echo_value($this->echo_value);
            }
        }
    }

    public function get_response_engine()
    {
        return $this->response;
    }

    public function should_handle()
    {
        return $this->should_handle;
    }
}