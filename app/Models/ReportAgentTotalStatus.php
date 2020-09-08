<?php

namespace App\Models;

/**
 * @property string $day
 * @property integer $agent_id
 * @property string $name
 * @property string $value
 * @property int $duration
 * @property string $created_at
 * @property string $updated_at
 */
class ReportAgentTotalStatus extends ReportAgentStatuses
{
    const TABLE_NAME = "report_agent_total_statuses";
    private static $out_arr;

    public $table = "";

    /**
     * @var array
     */
    protected $fillable = ['day', 'agent_id', 'name', 'value', 'duration', 'created_at', 'updated_at'];
    public function fillTable()
    {
        //self::$out_arr = $this->getLast();
        //print_r(self::$out_arr);exit;
        $this->deleteTheseAreContinue();
        \DB::table(ReportAgentStatusesGroup::TABLE_NAME)->where('is_processed', false)/*->where('created_at', '<', date('Y-m-d H:i:s'))*/
            ->orderBy('time_start')->orderBy('agent_id')->chunk(100, function ($statuses, $index) {

                foreach ($statuses as $status_data) {
                    if(102325000072297101 == $this->_getIdVal($status_data))
                    {
                        $is_end_date = !in_array($status_data->time_end,['0000-00-00 00:00:00',null]);
                        if(!$is_end_date)
                        {
                            $status_data->time_end = date('Y-m-d H:i:s');//$day_start . ' 23:59:59';
                        }

                        $i_datetime_start = strtotime($status_data->time_start);
                        $i_datetime_end = strtotime($status_data->time_end);
                        $day_start = date('Y-m-d', $i_datetime_start);
                        $day_end = date('Y-m-d', $i_datetime_end);
                        $time = date('H:i:s', $i_datetime_start);

                        if($day_start == $day_end)
                        {

                        }
                        else if($day_start < $day_end)
                        {
                            do{
                                $daytime_end = $day_start.' 23:59:59';
                                $duration = strtotime($daytime_end) - strtotime($status_data->time_start);
                                echo '>>'.$duration . "= ".$status_data->time_end . " ".$status_data->time_start."\n";

                                self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value] = $duration;

                                $status_data->time_start = date('Y-m-d', strtotime($status_data->time_start." +1 day")).' 00:00:00';
                                $day_start = date('Y-m-d', strtotime($status_data->time_start));

                                // Тут проверка на $is_end_date не нужна, мерям по дням. Если днь окончен то и дата окончания известна.
                                $this->add([
                                    'day' => $day_start,
                                    'agent_id' => $this->_getIdVal($status_data),
                                    'name' => $status_data->status_name,
                                    'value' => $status_data->status_value,
                                    'duration' => $duration,
                                    'is_continue' => false,
                                ]);

                            }while($day_start < $day_end);
                        }
                        $day_start = date('Y-m-d', strtotime($status_data->time_start));
                        $duration = strtotime($status_data->time_end) - strtotime($status_data->time_start);
                        echo $duration . "= ".$status_data->time_end . " ".$status_data->time_start."\n";
                         if(!isset($result[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value])){
                             self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value] = $duration;
                         }
                         self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value] += $duration;

                        $this->add([
                            'day' => $day_start,
                            'agent_id' => $this->_getIdVal($status_data),
                            'name' => $status_data->status_name,
                            'value' => $status_data->status_value,
                            'duration' => $duration,
                            'is_continue' => !$is_end_date,
                        ]);
                        /*else if($time != '00:00:00')
                        {
                            $status_data->time_start = $day_start.' 00:00:00';
                            \DB::table(ReportAgentStatusesGroup::TABLE_NAME)->
                            select(['status_value'])->
                            where('agent_id', '=', $this->_getIdVal($status_data))->
                            where('status_name', '=', $status_data->status_name)->
                            where('status_value', '=', $status_data->status_value)->
                            where('time_start', '<', $status_data->time_start)->max('time_start');
                        }*/
                        if($is_end_date)
                        {
                            \DB::table(ReportAgentStatusesGroup::TABLE_NAME)
                                ->where('agent_id', $status_data->agent_id)
                                ->where('status_name', $status_data->status_name)
                                ->where('time_start', $status_data->time_start)
                                ->update(['is_processed' => true]);
                        }
                    }
                }

                //return false;
                if(empty($statuses)/* || self::$time_start + 900 < time()*/)
                {
                    return false;
                }
            });
       /* foreach(self::$out_arr as $day=>$agents){

            foreach($agents as $agent=>$status_names){

                foreach($status_names as $status_name=>$status_values){

                    foreach($status_values as $status_value=>$duration){

                    }
                }
            }
        }*/
        print_r(self::$out_arr);
        self::$out_arr = [];
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
    public function add($data){

        $sql ='insert into report_agent_total_statuses (day, agent_id, name, value, duration, is_continue, created_at) values ("'.$data['day'].'", '.$data['agent_id'].', "'.$data['name'].'", "'.$data['value'].'", '.$data['duration'].', '.($data['is_continue']?'true':'false').', "'.date("Y-m-d H:i:s").'") ON DUPLICATE KEY UPDATE duration=duration+'.$data['duration'].';';

        \DB::insert($sql);
/*
        $sql = 'select report_unattended_call.agent_id, count(report_unattended_call.id) as count, DATE_FORMAT((report_unattended_call.time_start),"%Y-%m-%d") as day from report_unattended_call GROUP BY report_unattended_call.agent_id, DATE_FORMAT(report_unattended_call.time_start,"%Y %M %d");';
        $grouped_data_by_days = \DB::query(
            $sql
        );
        if(!empty($grouped_data_by_days))
        {
            $this->insert($grouped_data_by_days);
        }
        \DB::table(self::TABLE_NAME)->insert(
            $data
        );*/
    }
    public function deleteTheseAreContinue(){

        \DB::table(self::TABLE_NAME)->where('is_continue', '=',true)->delete();
    }


    public function getSortableFields()
    {
        return ['day'];
    }

    protected function getDefaultOrderField()
    {
        return 'day';
    }

    const PAGES_PER_PAGE = 20;
    public function getList($dateStart = '', $period = '',
                                 $searchWord = '', $sortField = 'day', $sortBy = 'desc',
                                 $page = 1): array
    {
        $searchWord = $this->getSearchWord($searchWord);
        $sortField = $this->getSortField($sortField);
        $sortBy = $this->getSortOrder($sortBy);
        $page = $this->getPage($page);

        [$dateFrom, $dateTo] = $this->getDateFromAndTo($this->getDateStart($dateStart), $this->getPeriod($period));
        $dateFrom .= ' 00:00:00';
        $dateTo .= ' 23:59:59';
        $call_list_q = self::query()->select([
            //'users.id as user_id',
            $this->table.'.day',
            $this->table.'.agent_id',
            $this->table.'.name',
            $this->table.'.value',
            $this->table.'.duration',
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
        $call_list_q->where('day', '>=', $dateFrom);
        $call_list_q->where('day', '<=', $dateTo);
        $call_list_q->orderBy( $sortField, $sortBy );

        if($searchWord)
        {
            $call_list_q->where(function ($q) use ($searchWord)
            {
                return $q->where('name', 'LIKE', '%'.$searchWord.'%')
                    ->orWhere('value', 'LIKE', '%'.$searchWord.'%')
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
        return [
            'data'        => $this->formatDataCallList( $call_list ),
            'pages_count' => $pages_count,
            'page'        => $page
        ];
    }

    public function formatDataCallList($status_list)
    {
        $result = [];
        foreach($status_list as $item)
        {
            $result[] = [
                'day'         => $item->day,//$item->user_id,
                'uid'         => $this->_getIdVal($item),//$item->user_id,
                'name'    => $item->name,
                'value'     => $item->value,
                'user_data'   => User::prepareUserData((array)$item->attributes),//( new User() )->getUserData( $item->agent_id ),
                'duration' => ReportAgentStatusesGroup::calculateDurationFromSeconds($item->duration)
            ];
        }
        return $result;
    }
}
