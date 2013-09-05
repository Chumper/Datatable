<?php namespace Chumper\Datatable;


use Exception;
use View;


class Table {

    private $columns = array();
    private $options = array();
    private $data = array();

    public function addColumn()
    {
        foreach (func_get_args() as $title)
        {
            if(is_array($title))
            {
                foreach ($title as $arrayTitle)
                {
                    $this->columns[] = $arrayTitle;
                }
            }
            else
                $this->columns[] = $title;
        }
        return $this;
    }

    public function countColumns()
    {
        return count($this->columns);
    }

    public function setOptions()
    {
        if(func_num_args() == 2)
        {
            $this->options[func_get_arg(0)] = func_get_arg(1);
        }
        else if(func_num_args() == 1 && is_array(func_get_arg(0)))
        {
            foreach (func_get_arg(0) as $key => $option)
            {
                $this->options[$key] = $option;
            }
        }
        else
            throw new Exception('Invalid number of options provided for the method "setOptions"');
        return $this;
    }

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function setUrl($url)
    {
        $this->options['sAjaxSource'] = $url;
        $this->options['bServerSide'] = true;
        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getData()
    {
        return $this->data;
    }

    public function render($view = null)
    {
        if(is_null($view))
            $view = 'datatable::template';

        return View::make($view,array(
            'options'   => $this->getOptions(),
            'data'      => $this->getData(),
            'columns'   => $this->columns,
        ));
    }
}