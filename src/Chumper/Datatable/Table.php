<?php namespace Chumper\Datatable;

use Exception;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

/**
 * Class Table
 * @package Chumper\Datatable
 */
class Table {

    /**
     * @var array
     */
    private $columns = array();
    /**
     * @var array
     */
    private $options = array();

    /**
     * @var array
     */
    private $callbacks = array();

    /**
     * Values to be sent to custom templates
     * 
     * @var array
     */
    private $customValues = array();

    /**
     * @var array
     */
    private $data = array();

    /**
     * @var boolean Determines if the template should echo the javascript
     */
    private $noScript = false;

    /**
     * @var String The name of the class the table will have later
     */
    protected $className;

    /**
     * @var boolean indicates if the mapping was already added to the options
     */
    private $createdMapping = true;

    /**
     * @var array name of mapped columns
     */
    private $aliasColumns = array();

    function __construct()
    {
        $this->className = str_random(8);
    }


    /**
     * @return $this
     */
    public function addColumn()
    {
        foreach (func_get_args() as $title)
        {
            if(is_array($title))
            {
                foreach ($title as $mapping => $arrayTitle)
                {
                    $this->columns[] = $arrayTitle;
                    $this->aliasColumns[] = $mapping;
                    if(is_string($mapping))
                    {
                        $this->createdMapping = false;
                    }
                }
            }
            else
            {
                $this->columns[] = $title;
                $this->aliasColumns[] = count($this->aliasColumns)+1;
            }
        }
        return $this;
    }

    /**
     * @return int
     */
    public function countColumns()
    {
        return count($this->columns);
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function setOptions()
    {
        if(func_num_args() == 2)
        {
           $this->options[func_get_arg(0)] =func_get_arg(1);
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

    /**
     * @return $this
     * @throws \Exception
     */
    public function setCallbacks()
    {
        if(func_num_args() == 2)
        {
            $this->callbacks[func_get_arg(0)] = func_get_arg(1);
        }
        else if(func_num_args() == 1 && is_array(func_get_arg(0)))
        {
            foreach (func_get_arg(0) as $key => $value)
            {
                $this->callbacks[$key] = $value;
            }
        }
        else
            throw new Exception('Invalid number of callbacks provided for the method "setCallbacks"');

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function setCustomValues()
    {
        if(func_num_args() == 2)
        {
            $this->customValues[func_get_arg(0)] = func_get_arg(1);
        }
        else if(func_num_args() == 1 && is_array(func_get_arg(0)))
        {
            foreach (func_get_arg(0) as $key => $value)
            {
                $this->customValues[$key] = $value;
            }
        }
        else
            throw new Exception('Invalid number of custom values provided for the method "setCustomValues"');

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->options['sAjaxSource'] = $url;
        $this->options['bServerSide'] = true;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }

    /**
     * @return array
     */
    public function getCustomValues()
    {
        return $this->customValues;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param null $view
     * @return mixed
     */
    public function render($view = null)
    {
        if(is_null($view))
            $view = 'datatable::template';

        if(!isset($this->options['sAjaxSource']))
        {
            $this->setUrl(Request::url());
        }

        // create mapping for frontend
        if(!$this->createdMapping)
        {
            $this->createMapping();
        }

        return View::make($view,array(
            'options'   => $this->options,
            'callbacks' => $this->callbacks,
            'values'    => $this->customValues,
            'data'      => $this->data,
            'columns'   => array_combine($this->aliasColumns,$this->columns),
            'noScript'  => $this->noScript,
            'class'     => $this->className,
        ));
    }

    /**
     * Instructs the table not to echo the javascript
     *
     * @return $this
     */
    public function noScript()
    {
        $this->noScript = true;
        return $this;
    }

    public function script($view = null)
    {
        if(is_null($view))
            $view = 'datatable::javascript';

        // create mapping for frontend
        if(!$this->createdMapping)
        {
            $this->createMapping();
        }

        return View::make($view,array(
            'options'   =>  $this->options,
            'callbacks' =>  $this->callbacks,
            'class'     =>  $this->className,
        ));
    }

    public function getClass()
    {
        return $this->className;
    }

    public function setClass($class)
    {
        $this->className = $class;
        return $this;
    }

    public function setAliasMapping($value)
    {
        $this->createdMapping = !$value;
        return $this;
    }

    //--------------------PRIVATE FUNCTIONS

    private function createMapping()
    {
        // set options for better handling
        // merge with existing options
        if(!array_key_exists('aoColumns', $this->options))
        {
            $this->options['aoColumns'] = array();
        }
        $matching = array();
        $i = 0;
        foreach($this->aliasColumns as $name)
        {
            if(array_key_exists($i,$this->options['aoColumns']))
            {
                $this->options['aoColumns'][$i] = array_merge_recursive($this->options['aoColumns'][$i],array('mData' => $name));
            }
            else
            {
                $this->options['aoColumns'][$i] = array('mData' => $name);
            }
            $i++;
        }
        $this->createdMapping = true;
        //dd($matching);
        return $matching;
    }
}