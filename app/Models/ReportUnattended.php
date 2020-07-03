<?php

namespace App\Models;

use App\Zoho\V1\Department;
use App\Zoho\V1\UnattendedCalls;
use Illuminate\Database\Eloquent\Model;

class ReportUnattended extends Model
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
    /**
     * @var array
     */
    private $a_agent_id_filter = [];

    protected $table = '';

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

    protected function checkSortField($field)
    {
        return !in_array(strtolower($field), [self::FIELD_TIME_CREATE, 'contact', 'first_name', 'business_name'/*,'department_name','team_name'*/]);
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

    public function loadFromRemoteServer()
    {
        // Делаю запросы к Зохо только если разница текущего врмеени и
        // последней созданной в БД записи больше 1го часа.
        // Не нужно часто дергать АПИ. Там есть лимиты https://www.zoho.com/recruit/api-new/api-limits.html
        $i_diff_dates = $this->diffNowAndLastCreation(ReportUnattendedCall::TABLE_NAME);
        $i_diff_unattended_dates = $this->diffNowAndLastCreation(ReportUnattendedGraph::TABLE_NAME);

        echo $i_diff_dates." ".$i_diff_unattended_dates;exit;
        if($i_diff_dates > 3600){
            $max_time_start_call = $this->maxTimeCreate(ReportUnattendedCall::TABLE_NAME);
            // Делаю выборку за день с существующего в БД. Поскольку в этот день выборка могла быть не полной.
            $o_uc = new UnattendedCalls(strtotime($max_time_start_call . " -1 day"));
            $a_agent_id = [];
            $o_users = new User();
            if($i_diff_dates > 10000)// С головы придуманное число. Идея в том что навряд ли агенты будут создаваться часто.
            {
                $o_department = new \App\Models\Department();
                $o_department_zoho = new Department();
                $a_department = $o_department_zoho->getDataArr();
                $o_department->insert($a_department);

                $a_uid_data = [];
                $o_team = new \App\Models\Team();
                $a_team = [];
                foreach($a_department as $item)
                {
                    $a_team_tmp = $o_department_zoho->getAllTeamDataArr($item['id']);
                    //print_r($a_team_tmp);
                    $a_team = array_merge($a_team, $a_team_tmp);
                    foreach($a_team_tmp as $team){
                        foreach($team['agents'] as $uid)
                        {
                            if(!isset($a_uid_data[$uid]))
                                $a_uid_data[$uid] = [
                                    'team_id' => [],
                                    'department_id' => $item['id']
                                ];

                            if(!isset($a_uid_data[$uid]['team_id'][$team['id']]))
                                $a_uid_data[$uid]['team_id'][$team['id']] = null;
                        }
                    }
                }
                $o_team->insert($a_team);

                $o_uc->setAAdditionalAgentsData($a_uid_data);
                $users_agent = $o_uc->getAgentsList();
                $a_agent_id = array_column($users_agent, 'user_id');
                $o_users->insert($users_agent);
            }else{
                $a_agent_id = User::getAllAgentIDs();
            }
            $a_agent_id = $this->filterUsers($a_agent_id);

            $o_calls = new ReportUnattendedCall();
            foreach($a_agent_id as $i_agent_id){
                $o_uc->setDataByAgent($i_agent_id);
                // In optimization purposes.
                $o_users->insert($o_uc->getAUsersClient());
                $o_uc->setAUsersClient([]);
                $o_calls->insert($o_uc->getAUnattended());
                $o_uc->setAUnattended([]);
            }

            (new ReportUnattendedGraph())->updateDB();
            (new ReportUnattendedCall())->updateDB();
        }
    }

    private function filterUsers(array $a_agent_id)
    {
        if($this->a_agent_id_filter){
            $a_agent_id = array_intersect($a_agent_id, $this->a_agent_id_filter);
        }
        return $a_agent_id;
    }

    /**
     * get Diagram list
     * @param $dateStart
     * @param $period
     * @return array
     */
    public function getDiagramList($dateStart = null, $period = null): array
    {
        return (new ReportUnattendedGraph($this->getAgentIdFilter()))->getList($dateStart, $period);
    }

    public function getCallList($dateStart, $period, $searchWord, $sortField, $sortBy, $page): array
    {
        return (new ReportUnattendedCall($this->getAgentIdFilter()))->getList($dateStart, $period, $searchWord, $sortField, $sortBy, $page);
    }

    public function maxTimeCreate($table): ?string
    {
        // TODO if column doesn't exists - please create its manually.
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
