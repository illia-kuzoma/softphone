<?php

namespace App\Models;

/**
 * @property integer $agent_id
 * @property string $status_name
 * @property string $status_value
 * @property string $time_start
 * @property string $time_end
 * @property string $created_at
 * @property string $updated_at
 */
class ReportAgentStatusesGroup extends ReportAgentStatuses
{
    const TABLE_NAME = "report_agent_statuses_groups";
    /**
     * @var int
     */
    public static $time_start;

    public $table = "";
    /**
     * @var array
     */
    protected $fillable = ['agent_id', 'status_name', 'status_value', 'time_start', 'time_end', 'created_at', 'updated_at'];

    public static $out_arr = [];
    public function fillTable()
    {
        self::$out_arr = $this->getLast();
        //print_r(self::$out_arr);exit;
        self::$time_start = time();
        //print_r(self::$out_arr);exit;
        \DB::table(AgentStatus::TABLE_NAME)->where('processed', false)
            ->orderBy('request_at')->orderBy('agent_id')->chunk(1000, function ($statuses, $index) {

            foreach ($statuses as $status_data) {

                foreach($status_data as $field_name=>$field_value)
                {
                    if(strripos($field_name, 'status') !== false)
                    {
                        $status_name = $field_name;
                        $index++;

                        if(empty(self::$out_arr[$status_data->agent_id]))
                        {
                            self::$out_arr[$status_data->agent_id] = [];
                        }

                        $default_structure = self::getBaseStructure($status_data->request_at, $field_value);
                        if(!isset(self::$out_arr[$status_data->agent_id][$status_name]))
                        {
                            $this->add([
                                'agent_id' => $status_data->agent_id,
                                'status_name' => $status_name,
                                'status_value' => $field_value,
                                'time_start' => $status_data->request_at,
                                'created_at' => date("Y-m-d H:i:s")
                            ]);
                            self::$out_arr[$status_data->agent_id][$status_name][] = $default_structure;
                        }
                        else
                        {
                            $i_count_statuses = count(self::$out_arr[$status_data->agent_id][$status_name]);
                            $i_status_last = $i_count_statuses-1;
                            $s_status_last = self::$out_arr[$status_data->agent_id][$status_name][$i_status_last]['status'];
                            $s_time_last = self::$out_arr[$status_data->agent_id][$status_name][$i_status_last]['time'];
                            //echo $status_data->agent_id.": name=".$status_name." ".$field_value ." ". $s_status_last ." ". $s_time_last ." < ". $status_data->request_at."\n";
                            if($field_value != $s_status_last && $s_time_last < $status_data->request_at)
                            {
                                \DB::beginTransaction();
                                try {
                                    $this->updateBefore([
                                        'agent_id' => $status_data->agent_id,
                                        'status_name' => $status_name,
                                        'time_start' => $s_time_last,
                                    ], ['time_end' => $status_data->request_at]);

                                    $this->add([
                                        'agent_id' => $status_data->agent_id,
                                        'status_name' => $status_name,
                                        'status_value' => $field_value,
                                        'time_start' => $status_data->request_at,
                                        'created_at' => date("Y-m-d H:i:s")
                                    ]);

                                    \DB::commit();
                                    // all good
                                } catch (\Exception $e) {
                                    \DB::rollback();
                                    // something went wrong
                                }
                                self::$out_arr[$status_data->agent_id][$status_name][] = $default_structure;
                            }
                        }
                    }
                }

                \DB::table(AgentStatus::TABLE_NAME)
                    ->where('agent_id', $status_data->agent_id)
                    ->where('created_at', $status_data->created_at)
                    ->update(['processed' => true]);
            }

            //return false;
            if(empty($statuses)/* || self::$time_start + 900 < time()*/)
            {
                return false;
            }
        });
        self::$out_arr = [];
    }
    public function add($data){

        \DB::table(self::TABLE_NAME)->insert(
            $data
        );
    }
    public function updateBefore($where = [], $data = []){

        \DB::table(self::TABLE_NAME)->where($where)->update(
            $data
        );
    }

    /**
     * Gets last statuses for user from DB, to continue from them.
     * @return array
     */
    public function getLast(): array
    {
        $a_out = [];
        $a_last_agent_status = \DB::select("select agent_id, status_name, status_value, time_start from ".self::TABLE_NAME." Where time_end='0000-00-00 00:00:00' || time_end IS NULL ORDER BY agent_id;");
        if(count($a_last_agent_status))
        {
            foreach($a_last_agent_status as $status_data)
            {
                $a_out[$status_data->agent_id][$status_data->status_name][] = self::getBaseStructure($status_data->time_start, $status_data->status_value);
            }
        }
        //print_r(count($a_last_agent_status));exit;
        return $a_out;
    }
    private static function getBaseStructure($time,$status)
    {
        return ['time'=>$time,'status'=>$status];
    }

    const PAGES_PER_PAGE = 20;
    public function getList($dateStart = '', $period = '',
                            $searchWord = '', $sortField = 'time_start', $sortBy = 'desc',
                            $page = 1): array
    {
        $searchWord = $this->getSearchWord($searchWord);
        $sortField = $this->getSortField($sortField);
        $sortBy = $this->getSortOrder($sortBy);
        $page = $this->getPage($page);

        [$dateFrom, $dateTo] = $this->getDateFromAndTo($this->getDateStart($dateStart), $this->getPeriod($period));
        $dateFrom .= ' 00:00:00';
        $dateTo .= ' 23:59:59';

        $call_list_q = ReportAgentStatusesGroup::query()->select([
            //'users.id as user_id',
            $this->table.'.agent_id',
            $this->table.'.status_name',
            $this->table.'.status_value',
            $this->table.'.time_start',
            $this->table.'.time_end',
            $this->table.'.created_at',
            'users.first_name',
            'users.last_name',
            'users.photo',
            'users.department_id',
            'users.team_id'
        ])->join('users', $this->table.'.agent_id', '=', 'users.id')
            /* ->join('users', 'departments.id', '=', 'users.department_id')*/;

        $a_filter_by_agents = $this->getAgentIdFilter();
        if(!empty($a_filter_by_agents))
            $call_list_q->whereIn($this->table.'.agent_id', $a_filter_by_agents);
//echo $dateFrom . " " . $dateTo;exit;
        $call_list_q->where('time_start', '>=', $dateFrom);
        $call_list_q->where('time_start', '<=', $dateTo);
        $call_list_q->orderBy( $sortField, $sortBy );

        if($searchWord)
        {
            $call_list_q->where(function ($q) use ($searchWord)
            {
                return $q->where('status_name', 'LIKE', '%'.$searchWord.'%')
                    ->orWhere('status_value', 'LIKE', '%'.$searchWord.'%')
                    ->orWhere('first_name', 'LIKE', '%'.$searchWord.'%')
                    ->orWhere('last_name', 'LIKE', '%'.$searchWord.'%');
            });
        }
        $calls_cnt = $call_list_q->count();
        $pages_count = floor( $calls_cnt / self::PAGES_PER_PAGE ) + (( $calls_cnt % self::PAGES_PER_PAGE ) === 0 ? 0 : 1);
        if($page > $pages_count){
            $page = $pages_count;
        }
        $call_list_q->offset(($page-1) * self::PAGES_PER_PAGE)->limit(self::PAGES_PER_PAGE);
        $call_list = $call_list_q->get();
        //print_r($call_list);exit;
        return [
            'data'        => $this->formatDataCallList( $call_list ),
            'pages_count' => $pages_count,
            'page'        => $page
        ];
    }

    /**
     * Format Call List to return to client app.
     *
     * @param $data
     * @param $page
     *
     * @return array
     */
    private function formatDataCallList($data): array
    {
        $result = [];
        if ( ! empty( $data ) )
        {
            foreach ( $data as $item )
            {
                $result[] = [
                    'uid'         => $this->_getIdVal($item),//$item->user_id,
                    'name'    => $item->status_name,
                    'value'     => $item->status_value,
                    'user_data'   => User::prepareUserData((array)$item->attributes),//( new User() )->getUserData( $item->agent_id ),
                    'time_start'    => $item->time_start,
                    'time_end'       =>  $item->time_end,
                    'duration' => $this->calculateDuration($item)
                ];
            }
        }

        return $result;
    }

    public function calculateDuration(self $o_status)
    {
        $s_duration = '';
        if($o_status->time_end < $o_status->time_start)
            return $s_duration;

        $i_duration = strtotime($o_status->time_end) - strtotime($o_status->time_start);

        $hours = floor($i_duration/3600);
        $minutes = floor(($i_duration-($hours*3600))/60);
        $seconds = floor($i_duration - ($hours*3600 + $minutes*60));
        $s_duration = $hours."h " . $minutes."m " . $seconds."s ";
        return $s_duration;
    }

    public function getTotalList($dateStart = '', $period = '',
                                 $searchWord = '', $sortField = 'time_start', $sortBy = 'desc',
                                 $page = 1): array
    {
        $searchWord = $this->getSearchWord($searchWord);
        $sortField = $this->getSortField($sortField);
        $sortBy = $this->getSortOrder($sortBy);
        $page = $this->getPage($page);

        [$dateFrom, $dateTo] = $this->getDateFromAndTo($this->getDateStart($dateStart), $this->getPeriod($period));
        $dateFrom .= ' 00:00:00';
        $dateTo .= ' 23:59:59';
        $call_list_q = ReportAgentStatusesGroup::query()->select([
            //'users.id as user_id',
            $this->table.'.agent_id',
            $this->table.'.status_name',
            $this->table.'.status_value',
            $this->table.'.time_start',
            $this->table.'.time_end',
            $this->table.'.created_at',
            'users.first_name',
            'users.last_name',
            'users.photo',
            'users.department_id',
            'users.team_id'
        ])->join('users', $this->table.'.agent_id', '=', 'users.id')
            /* ->join('users', 'departments.id', '=', 'users.department_id')*/;

        $a_filter_by_agents = $this->getAgentIdFilter();
        if(!empty($a_filter_by_agents))
            $call_list_q->whereIn($this->table.'.agent_id', $a_filter_by_agents);
//echo $dateFrom . " " . $dateTo;exit;
        $call_list_q->where('time_start', '>=', $dateFrom);
        $call_list_q->where('time_start', '<=', $dateTo);
        $call_list_q->orderBy( $sortField, $sortBy );

        if($searchWord)
        {
            $call_list_q->where(function ($q) use ($searchWord)
            {
                return $q->where('status_name', 'LIKE', '%'.$searchWord.'%')
                    ->orWhere('status_value', 'LIKE', '%'.$searchWord.'%')
                    ->orWhere('first_name', 'LIKE', '%'.$searchWord.'%')
                    ->orWhere('last_name', 'LIKE', '%'.$searchWord.'%');
            });
        }
        $calls_cnt = $call_list_q->count();
        $pages_count = floor( $calls_cnt / self::PAGES_PER_PAGE ) + (( $calls_cnt % self::PAGES_PER_PAGE ) === 0 ? 0 : 1);
        if($page > $pages_count){
            $page = $pages_count;
        }
        $call_list_q->offset(($page-1) * self::PAGES_PER_PAGE)->limit(self::PAGES_PER_PAGE);
        $call_list = $call_list_q->get();
        print_R($call_list);exit;
    }
}
