<?php

namespace App\Models;

use App\Zoho\Calls;
use Illuminate\Database\Eloquent\Model;

class ReportMissed extends Model
{
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

    public function getPeriod($period)
    {
        if($this->checkPeriod($period))
        {
            $period = 'day';
        }
        return $period;
    }

    protected function checkPeriod($period)
    {
        return $period == '-' || !$period || !in_array($period,
                [self::PERIOD_DAY,self::PERIOD_WEEK,self::PERIOD_MONTH,self::PERIOD_YEAR]);
    }

    public function getDateFromAndTo($dateStart, $period)
    {
        $dateStart = strtotime( $dateStart );
        $dateFrom = $dateTo = date( self::DATE_DAY_FORMAT, $dateStart) ;
        // set period
        $currentDayOfWeek = date('w', $dateStart);
        switch (strtolower($period)) {
            case self::PERIOD_WEEK:
                $dateFrom = (date( self::DATE_DAY_FORMAT, strtotime( '-' . $currentDayOfWeek . ' days',  $dateStart ) ));
                $dateTo   = (date( self::DATE_DAY_FORMAT, strtotime( '+' . ( 6 - $currentDayOfWeek ) . ' days', $dateStart ) ));
                break;
            case self::PERIOD_MONTH:
                $dateFrom = (date(self::DATE_DAY_FORMAT, strtotime('first day of this month', $dateStart) ));
                $dateTo = (date(self::DATE_DAY_FORMAT, strtotime('last day of this month', $dateStart) ));
                break;
            case self::PERIOD_YEAR:
                $dateFrom = (date(self::DATE_DAY_FORMAT, strtotime('first day of January', $dateStart) ));
                $dateTo = (date(self::DATE_DAY_FORMAT, strtotime('last day of December', $dateStart) ));
                break;
            case self::PERIOD_DAY:
            default:
                break;
        }
        return [$dateFrom, $dateTo];
    }

    public function getUid($uid)
    {
        if($this->checkUid($uid))
        {
            $uid = null;
        }
        return $uid;
    }

    protected function checkUid($uid)
    {
        return $uid == '-' || !$uid;
    }

    public function getSearchWord($word)
    {
        $word = htmlspecialchars($word);
        if($this->checkSearchWord($word))
        {
            $word = '';
        }
        return $word;
    }

    protected function checkSearchWord($word)
    {
        return $word == '-' || !$word;
    }

    public function getSortField($field)
    {
        if($this->checkSortField($field)){
            $field = self::FIELD_TIME_CREATE;
        }
        return $field;
    }

    protected function checkSortField($field)
    {
        return !in_array(strtolower($field),[self::FIELD_TIME_CREATE,'contact', 'first_name', 'business_name']);
    }

    public function getSortOrder($order)
    {
        if($this->checkSortBy($order)){
            $order = self::ORDER_DESC;
        }
        return $order;
    }

    protected function checkSortBy($by)
    {
        return !in_array(strtolower($by),[self::ORDER_ASC,self::ORDER_DESC]);
    }

    public function getPage($page)
    {
        if($this->checkPage($page))
        {
            $page = 1;
        }
        return $page;
    }

    protected function checkPage(string $page)
    {
        $page = (int)$page;
        return (int)$page < 1;
    }

    protected function getDateStart($dateStart)
    {
        if($this->checkDate($dateStart))
        {
            $dateStart = date(self::DATE_DAY_FORMAT);
        }
        return $dateStart;
    }

    protected function checkDate($date)
    {
        return $date == '-' || !$date ||
            (date(self::DATE_DAY_FORMAT, strtotime($date)) != $date);
    }

    public function loadFromRemoteServer()
    {
        // Делаю запросы к Зохо только если разница текущего врмеени и
        // последней созданной в БД записи больше 1го часа.
        // Не нужно часто дергать АПИ. Там есть лимиты https://www.zoho.com/recruit/api-new/api-limits.html

        if((time() - strtotime($this->maxRecordTimeCreate())) > 3600)
        {
            $max_time_start_call = $this->maxTimeCreate();
            // Делаю выборку за день с существующего в БД. Поскольку в этот день выборка могла быть не полной.
            $time_start_call = date("c", strtotime($max_time_start_call." -1 day"));
            $zoho_calls = new Calls();
            $activities_list = $zoho_calls->getActivities($time_start_call, false);
            $this->updateDB($activities_list, $time_start_call);
        }
    }
}
