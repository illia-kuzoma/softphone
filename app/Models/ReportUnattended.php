<?php

namespace App\Models;

use App\Zoho\V1\UnattendedCalls;
use Illuminate\Database\Eloquent\Model;

class ReportUnattended extends Model
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

    protected function _getIdKey(){
        return 'agent_id'; // 'user_id'
    }
    protected function _getIdVal($item){
        return (string)$item->{$this->_getIdKey()}; //
    }

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
        if($this->diffNowAndLastCreation() > 3600)
        {
            $max_time_start_call = $this->maxTimeCreate();
            // Делаю выборку за день с существующего в БД. Поскольку в этот день выборка могла быть не полной.
            $o_uc = new UnattendedCalls(strtotime($max_time_start_call." -30 day"));
            $a_agent_id = [];
            $o_users = new User();
            if($this->diffNowAndLastCreation() > 100000)
                // С головы придуманное число. Идея в том что навряд ли агенты будут создаваться часто.
            {
                $users_agent = $o_uc->getAgentsList();
                $a_agent_id = array_column($users_agent, 'user_id');
                $o_users->insert($users_agent);
            }
            else
            {
                $a_agent_id = User::query()->where('role', '=', User::ROLE_AGENT)->pluck('id')->toArray();
            }
            foreach($a_agent_id as $i_agent_id)
            {
                $o_uc->setDataByAgent($i_agent_id);
                $o_users->insert($o_uc->getAUsersClient());
                $o_uc->setAUsersClient([]);
                $this->insert($o_uc->getAUnattended());
                $o_uc->setAUnattended([]);
            }

            (new ReportUnattendedGraph())->updateDB();
        }
    }
}
