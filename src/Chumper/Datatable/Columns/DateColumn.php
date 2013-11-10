<?php namespace Chumper\Datatable\Columns;

class DateColumn extends BaseColumn {

    /**
     * Constants for the time representation
     */
    const DATE = 0;
    const TIME = 1;
    const DATE_TIME = 2;
    const CUSTOM = 4;
    const FORMATTED_DATE = 5;
    const DAY_DATE = 6;

    /**
     * @var int The format to show
     */
    private $format;

    /**
     * @var string custom show string if chosen
     */
    private $custom;

    function __construct($name, $format = 2, $custom = "")
    {
        parent::__construct($name);
        $this->format = $format;
        $this->custom = $custom;
    }

    /**
     * @param mixed $model The data to pass to the column,
     *              could be a model or an array
     * @return mixed the return value of the implementation,
     *              should be text in most of the cases
     */
    public function run($model)
    {
        switch($this->format)
        {
            case DateColumn::DATE:
                return $model[$this->name]->toDateString();
                break;
            case DateColumn::TIME:
                return $model[$this->name]->toTimeString();
                break;
            case DateColumn::DATE_TIME:
                return $model[$this->name]->toDateTimeString();
                break;
            case DateColumn::CUSTOM:
                return $model[$this->name]->format($this->custom);
                break;
            case DateColumn::FORMATTED_DATE:
                return $model[$this->name]->toFormattedDateString();
                break;
            case DateColumn::DAY_DATE:
                return $model[$this->name]->toDayDateTimeString();
                break;

        }
    }
}