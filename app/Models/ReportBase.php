<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportBase extends Model
{

    const TABLE_NAME = '';
    const DATE_DAY_FORMAT = 'Y-m-d';
    const DATE_TIME_FORMAT = "Y-m-d H:i:s";
    const FIELD_TIME_CREATE = 'time_start';
    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';
    const PERIOD_DAY = 'day';
    const PERIOD_MONTH = 'month';
    const PERIOD_WEEK = 'week';
    const PERIOD_YEAR = 'year';
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    protected function getDefaultOrderField()
    {
        return self::FIELD_TIME_CREATE;
    }

}
