<?php

namespace App\Models;

use App\Models\Glob\DateTime;

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
    private static $out_arr = [];
    private static $a_to_processed = [];

    public $table = "";

    public static $i = 0;
    /**
     * @var array
     */
    protected $fillable = ['day', 'agent_id', 'name', 'value', 'duration', 'created_at', 'updated_at'];
    public function fillTable()
    {
        //self::$out_arr = $this->getLast();
        //print_r(self::$out_arr);exit;
        $this->deleteTheseAreContinue();
        \DB::table(ReportAgentStatusesGroup::TABLE_NAME)->
        where('is_processed', '=',false)->
        /*where('agent_id', '=',102325000022393189)->*/
        where('time_start' ,'>', '2020-09-01')->
        orderBy('time_start')->orderBy('agent_id')->
        chunk(1000, function ($statuses, $index) {
            foreach ($statuses as $status_data) {
                if(1 /*|| 102325000022393189 == $this->_getIdVal($status_data)
                    && strtotime($status_data->time_start) > strtotime('2020-09-01')*/)
                {
                    $time_start = $status_data->time_start;
                    $not_has_continue_statuses_in_that_day = true;
                    $is_end_date = !in_array($status_data->time_end,['0000-00-00 00:00:00',null]);
                    if(!$is_end_date)
                    {
                        $status_data->time_end = (new DateTime())->getDateTime(); //date('Y-m-d H:i:s');//$day_start . ' 23:59:59';
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
                            //echo '>>'.$duration . "= ".$status_data->time_end . " ".$status_data->time_start."->".$day_start."  ".$status_data->status_name . " " .$status_data->status_value."\n";

                            //self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value] = $duration;

                            // Тут проверка на $is_end_date не нужна, мерям по дням. Если днь окончен то и дата окончания известна.
                            /*$this->add([
                                'day' => $day_start,
                                'agent_id' => $this->_getIdVal($status_data),
                                'name' => $status_data->status_name,
                                'value' => $status_data->status_value,
                                'duration' => $duration,
                                'is_continue' => false,
                            ]);*/
                            if(!isset(self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['duration'])){
                                self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['duration'] = 0;
                                self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['is_continue'] = !$is_end_date;
                            }
                            elseif(!self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['is_continue'] && !$is_end_date)
                            {
                                $not_has_continue_statuses_in_that_day = false;
                                self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['is_continue'] = true;
                            }
                            self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['duration'] += $duration;

                            $status_data->time_start = date('Y-m-d', strtotime($status_data->time_start." +1 day")).' 00:00:00';
                            $day_start = date('Y-m-d', strtotime($status_data->time_start));

                        }while(strtotime($day_start) < strtotime($day_end));
                    }
                    $day_start = date('Y-m-d', strtotime($status_data->time_start));
                    $duration = strtotime($status_data->time_end) - strtotime($status_data->time_start);
                    //echo '>>'.$duration . "= ".$status_data->time_end . " ".$status_data->time_start."->".$day_start."  ".$status_data->status_name . " " .$status_data->status_value."\n";

                    /* if(!isset($result[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value])){
                         self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value] = $duration;
                     }
                     self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value] += $duration;*/

                     $is_current_day = date('Y-m-d') == $day_start;
                     if($is_current_day) // Если сегодня и день изменений события это тот же день
                     {
                         /*$this->add([
                             'day' => $day_start,
                             'agent_id' => $this->_getIdVal($status_data),
                             'name' => $status_data->status_name,
                             'value' => $status_data->status_value,
                             'duration' => $duration,
                             'is_continue' => true, // так как всё ещё может изменится, день не завершён.
                         ]);*/
                         if(!isset(self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['duration'])){
                             self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['duration'] = 0;
                             self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['is_continue'] = true;
                         }
                         elseif(!self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['is_continue'])
                         {
                             $not_has_continue_statuses_in_that_day = false;
                             self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['is_continue'] = true;
                         }
                         self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['duration'] += $duration;
                     }
                     else
                     {
                        // echo $day_start." ".$duration . "= ".$status_data->time_end . " ".$status_data->time_start." ".$status_data->status_name . " " .$status_data->status_value."\n";
                         //echo $day_start." ".$duration."\n";
                         // Всегда диапазон не сегодняшнего дня, который начался и кончился в тот же день.
                         if(!isset(self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['duration'])){
                             self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['duration'] = 0;
                             self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['is_continue'] = !$is_end_date;
                         }
                         elseif(!self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['is_continue'] && !$is_end_date)
                         {
                             $not_has_continue_statuses_in_that_day = false;
                             self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['is_continue'] = true;
                         }
                         self::$out_arr[$day_start][$this->_getIdVal($status_data)][$status_data->status_name][$status_data->status_value]['duration'] += $duration;
                         //print_r(self::$out_arr);
                        /* $this->add([
                             'day' => $day_start,
                             'agent_id' => $this->_getIdVal($status_data),
                             'name' => $status_data->status_name,
                             'value' => $status_data->status_value,
                             'duration' => $duration,
                             'is_continue' => false,
                         ]);*/
                     }
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
                    //echo '$is_end_date='.$is_end_date.'$is_current_day='.$is_current_day.' $not_has_continue_statuses_in_that_day='.$not_has_continue_statuses_in_that_day. "\n";
                    if(1 && $is_end_date && !$is_current_day && $not_has_continue_statuses_in_that_day){
                        self::$a_to_processed[] = [
                            'agent_id' => $status_data->agent_id,
                            'status_name' => $status_data->status_name,
                            'status_value' => $status_data->status_value,
                            'time_start' => $time_start,
                        ];
                    }
                }
            }
            //return false;
           /* if(empty($statuses))
            {
                return false;
            }*/
        });

        $i_c = 0;
        foreach(self::$out_arr as $day=>$agents){

            foreach($agents as $agent=>$status_names){

                foreach($status_names as $status_name=>$status_values){

                    foreach($status_values as $status_value=>$duration){
                        $data = [
                            'day' => $day,
                            'agent_id' => $agent,
                            'name' => $status_name,
                            'value' => $status_value,
                            'duration' => $duration['duration'],
                            'is_continue' => $duration['is_continue'],
                        ];
                        $i_c++;
                        //print_r($data);continue;
                        $this->add($data);
                    }
                }
            }
        }
//echo 'count self::$a_to_processed'.count(self::$a_to_processed)."\n";
        foreach(self::$a_to_processed as $item)
        {
            \DB::table(ReportAgentStatusesGroup::TABLE_NAME)
                ->where('agent_id', $item['agent_id'])
                ->where('status_name', $item['status_name'])
                ->where('status_value', $item['status_value'])
                ->where('time_start', $item['time_start'])
                ->update(['is_processed' => true]);
        }
        //echo 'count $i_c='.$i_c."\n";
        //print_r(self::$out_arr);
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

        $date_time = (new DateTime())->getDateTime(); // date("Y-m-d H:i:s")
        /*$sql ='insert into report_agent_total_statuses (day, agent_id, name, value, duration, is_continue, created_at) values ("'.$data['day'].'", '.$data['agent_id'].', "'.$data['name'].'", "'.$data['value'].'", '.$data['duration'].', '.($data['is_continue']?'true':'false').', "'.date("Y-m-d H:i:s").'")
        ON DUPLICATE KEY UPDATE duration=if(is_continue,duration+'.$data['duration'].',duration);';*/
        $sql ='insert into report_agent_total_statuses (day, agent_id, name, value, duration, is_continue, created_at) values ("'
            .$data['day'].'", '.$data['agent_id'].', "'.$data['name'].'", "'.$data['value'].'", '
            .$data['duration'].', '.($data['is_continue']?'true':'false').
            ', "'.$date_time.'") ON DUPLICATE KEY UPDATE duration=duration'; // TODO Может вместо Игнора сделать замену старого на новое!!! Нужно посмотреть. проверить!
       //         ON DUPLICATE KEY UPDATE duration='.$data['duration'].';
        //echo( self::$i++).' '.$sql."\n";
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
        return ['day', 'first_name', 'value', 'duration','name'];
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

        $pages_count = 1;
        $call_list = $this->getStatusListData($dateStart, $period, $searchWord, $sortField, $sortBy, $page,$pages_count);
        return [
            'data'        => $this->formatDataCallList( $call_list ),
            'pages_count' => $pages_count,
            'page'        => $page
        ];
    }

    private function getStatusListData($dateStart = '', $period = '',
                                       $searchWord = '', $sortField = 'day', $sortBy = 'desc',
                                       &$page, &$pages_count)
    {
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

        // Исключаю записи в которых поле name имеет указанное в функции except.
        $this->except($call_list_q, 'name');

        $a_filter_by_agents = $this->getAgentIdFilter();
        if(!empty($a_filter_by_agents))
            $call_list_q->whereIn($this->table.'.agent_id', $a_filter_by_agents);

        $a_filter_by_types = $this->getTypeNameFilter();
        if(!empty($a_filter_by_types))
            $call_list_q->whereIn($this->table.'.name', $a_filter_by_types);

        $a_filter_by_values = $this->getTypeValueFilter();
        if(!empty($a_filter_by_values))
            $call_list_q->whereIn($this->table.'.value', $a_filter_by_values);

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
        if($page)
        {
            $calls_cnt = $call_list_q->count();
            $pages_count = floor( $calls_cnt / self::PAGES_PER_PAGE ) + (( $calls_cnt % self::PAGES_PER_PAGE ) === 0 ? 0 : 1);
            if($page > $pages_count){
                $page = $pages_count;
            }
            $call_list_q->offset(($page-1) * self::PAGES_PER_PAGE)->limit(self::PAGES_PER_PAGE);
        }
        $call_list = $call_list_q->get();
        /*print_r($call_list->toArray());
        echo"_____________";*/
        return $call_list;
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

    /**
     * Add 2 charts, one for Status Type = Status, one for Status Type = Phone Status
     *  X Axis is Agent, and then Status Value
     *  Y axis is total time spent
     *
     * @param $dateStart
     * @param $period
     * @return array
     */
    public function getGraphList($dateStart, $period): array
    {
        $page = 0;
        $page_count = 0;
        $a_statuses = ['status', 'phone_status']; // statuses for selection.
        $status_list = $this->getStatusListData($dateStart, $period,'', 'day', 'asc', $page, $page_count);

        //print_r($status_list->toArray());exit;
        $result = $result_tmp = [];
        if ( ! empty( $status_list ) ) {
            foreach ( $status_list as $item ) {
                if(in_array($item->name, $a_statuses))
                {
                    $key = $this->_getIdVal($item).' '.$item->value;
                    if(!isset($result_tmp[$item->name][$key]))
                    {
                        $result_tmp[$item->name][$key]= [
                            'uid' => $this->_getIdVal($item),
                            'first_name' => $item->first_name,
                            'last_name' => $item->last_name,
                            'x' => $item->first_name . ' ' . $item->last_name.', '. $item->value,
                            'y' => $item->duration
                        ];
                    }
                    else
                    {
                        $result_tmp[$item->name][$key]['y'] += $item->duration;
                    }
                }
            }
        }

        foreach($result_tmp as $k=>&$item )
        {
            usort($item, function($a, $b){
                return ($a['x'] <=> $b['x']);
            });
        }
        unset($item);
//print_r(json_encode($result_tmp));exit;
        $a_status_values = \DB::table(ReportAgentStatusesGroup::TABLE_NAME)->select([
            'status_name',  'status_value'
        ])->whereIn('status_name', $a_statuses)->groupBy(['status_value','status_name'])->get()->toArray();
        $a_status_name_value = [];
        foreach($a_status_values as $item){
            $a_status_name_value[$item->status_name][] = $item->status_value;
        }

        foreach($result_tmp as $k=>$item )
        {
            foreach($item as &$user){
                $user['total_time'] = ReportAgentStatusesGroup::calculateDurationFromSeconds($user['y']);
                $user['y'] = ceil($user['y']/60);
            }
            unset($user);
            $result[] = [
                'name' => $k,
                'data' => array_values($item),
                'filter_values' => $a_status_name_value[$k]
            ];
        }

        return $result;
    }
}
