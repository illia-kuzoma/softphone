<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $agent_id
 * @property string $status_name
 * @property string $status_value
 * @property string $start_at
 * @property string $created_at
 * @property string $updated_at
 */
class AgentStatusesGroup extends Model
{
    const TABLE_NAME = "agent_statuses_groups";

    public $table = "";
    /**
     * @var array
     */
    protected $fillable = ['agent_id', 'status_name', 'status_value', 'start_at', 'created_at', 'updated_at'];

    public function __construct()
    {
        $this->table = self::TABLE_NAME;
        parent::__construct();
    }

public static $out_arr = [];
    public function fillTable()
    {
        self::$out_arr = $this->getLast();
        //print_r(self::$out_arr);exit;
        \DB::table(AgentStatus::TABLE_NAME)->where('processed', false)->orderBy('request_at')->orderBy('agent_id')->chunk(100, function ($statuses, $index) {

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
                                'start_at' => $status_data->request_at,
                                'created_at' => date("Y-m-d H:i:s")
                            ]);
                            self::$out_arr[$status_data->agent_id][$status_name][] = $default_structure;
                        }
                        else
                        {
                            $i_count_statuses = count(self::$out_arr[$status_data->agent_id][$status_name]);
                            $i_status_last = $i_count_statuses-1;
                            $s_status_last = self::$out_arr[$status_data->agent_id][$status_name][$i_status_last]['status'];
                            if($field_value != $s_status_last)
                            {
                                $this->add([
                                    'agent_id' => $status_data->agent_id,
                                    'status_name' => $status_name,
                                    'status_value' => $field_value,
                                    'start_at' => $status_data->request_at,
                                    'created_at' => date("Y-m-d H:i:s")
                                ]);
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
            if(empty($statuses))
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

    /**
     * Gets last statuses for user from DB, to continue from them.
     * @return array
     */
    public function getLast(): array
    {
        $a_out = [];
        $a_last_agent_status = \DB::select("select agent_id, status_name, status_value, max(start_at) as last_start_at from agent_statuses_groups Group by agent_id, status_name ORDER BY agent_id;");
        if(count($a_last_agent_status))
        {
            foreach($a_last_agent_status as $status_data)
            {
                $a_out[$status_data->agent_id][$status_data->status_name][] = self::getBaseStructure($status_data->last_start_at, $status_data->status_value);
            }
        }
        return $a_out;
    }
    private static function getBaseStructure($time,$status)
    {
        return ['time'=>$time,'status'=>$status];
    }
}
