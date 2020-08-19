<?php


namespace App\Models\Traits;


trait Report
{
    /**
     * @var array
     */
    private $a_agent_id_filter = [];

    /**
     * ReportUnattended constructor.
     * @param array $a_agent_id_filter
     */
    public function __construct(array $a_agent_id_filter = [])
    {
        $this->table = static::TABLE_NAME;
        $this->a_agent_id_filter = $a_agent_id_filter;
    }

    /**
     * @return array
     */
    public function getAgentIdFilter(): array
    {
        return $this->a_agent_id_filter;
    }

    protected function _getIdKey()
    {
        return 'agent_id'; // 'user_id'
    }

    protected function _getIdVal($item)
    {
        return (string)$item->{$this->_getIdKey()}; //
    }

    public function setAgentIdFilter(array $a_uid)
    {
        if(!empty($a_uid)){
            $this->a_agent_id_filter = $a_uid;
        }
    }

    public function getPeriod($period)
    {
        if($this->checkPeriod($period)){
            $period = self::PERIOD_WEEK;
        }
        return $period;
    }

    protected function checkPeriod($period)
    {
        return $period == '-' || !$period || !in_array($period,
                [self::PERIOD_DAY, self::PERIOD_WEEK, self::PERIOD_MONTH, self::PERIOD_YEAR]);
    }

    public function getDateFromAndTo($dateStart, $period)
    {
        $dateStart = strtotime($dateStart);
        $dateFrom = $dateTo = date(self::DATE_DAY_FORMAT, $dateStart);
        // set period
        $currentDayOfWeek = date('w', $dateStart);
        switch(strtolower($period)){
            case self::PERIOD_WEEK:
                $dateFrom = (date(self::DATE_DAY_FORMAT, strtotime('-' . $currentDayOfWeek . ' days', $dateStart)));
                $dateTo = (date(self::DATE_DAY_FORMAT, strtotime('+' . (6 - $currentDayOfWeek) . ' days', $dateStart)));
                break;
            case self::PERIOD_MONTH:
                $dateFrom = (date(self::DATE_DAY_FORMAT, strtotime('first day of this month', $dateStart)));
                $dateTo = (date(self::DATE_DAY_FORMAT, strtotime('last day of this month', $dateStart)));
                break;
            case self::PERIOD_YEAR:
                $dateFrom = (date(self::DATE_DAY_FORMAT, strtotime('first day of January', $dateStart)));
                $dateTo = (date(self::DATE_DAY_FORMAT, strtotime('last day of December', $dateStart)));
                break;
            case self::PERIOD_DAY:
            default:
                break;
        }
        return [$dateFrom, $dateTo];
    }

    public function getUid($uid)
    {
        if($this->checkUid($uid)){
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
        if($this->checkSearchWord($word)){
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

    public function checkSortField($field)
    {
        return !in_array(strtolower($field), $this->getSortableFields());
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
        return !in_array(strtolower($by), [self::ORDER_ASC, self::ORDER_DESC]);
    }

    public function getPage($page)
    {
        if($this->checkPage($page)){
            $page = 1;
        }
        return $page;
    }

    protected function checkPage($page)
    {
        $page = (int)$page;
        return (int)$page < 1;
    }

    protected function getDateStart($dateStart)
    {
        if($this->checkDate($dateStart)){
            $dateStart = date(self::DATE_DAY_FORMAT);
        }
        return $dateStart;
    }

    protected function checkDate($date)
    {
        return $date == '-' || !$date ||
            (date(self::DATE_DAY_FORMAT, strtotime($date)) != $date);
    }

    private function filterUsers(array $a_agent_id)
    {
        if($this->a_agent_id_filter){
            $a_agent_id = array_intersect($a_agent_id, $this->a_agent_id_filter);
        }
        return $a_agent_id;
    }

    public function maxTimeStart($table): ?string
    {
        return \DB::table($table)->max('time_start');
    }

    public function maxRecordTimeCreate($table): ?string
    {
        return \DB::table($table)->max('created_at');
    }

    public function diffNowAndLastCreation($table): int
    {
        $date = $this->maxRecordTimeCreate($table);
        if(empty($date)){
            return 999999999;
        }
        return time() - strtotime($date);
    }
}
