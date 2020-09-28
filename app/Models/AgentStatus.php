<?php

namespace App\Models;

use App\Models\Glob\DateTime;
use App\Zoho\V1\agentAvailability;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $agent_id
 * @property string $chat_status
 * @property string $phone_mode
 * @property string $phone_status
 * @property string $mail_status
 * @property string $presence_status
 * @property string $status
 * @property string $request_at
 * @property string $created_at
 */
class AgentStatus extends Model
{
    use Common;

    const TABLE_NAME = "agent_statuses";

    public $table = "";
    /**
     * @var array
     */
    protected $fillable = ['agent_id', 'chat_status', 'phone_mode', 'phone_status', 'mail_status', 'presence_status', 'status', 'request_at', 'created_at'];

    private static $is_in_process = false;

    public function tableIsFilling()
    {

    }

    public function __construct()
    {
        $this->table = self::TABLE_NAME;
        parent::__construct();
    }

    /**
     * Insert Single Call Data to DB
     * @param $singleCallData
     */
    public function insertSingleData($singleCallData): void
    {
        if ( $this->validateBeforeInsert($singleCallData) ) {
            $res = \DB::table( $this->table )->insert(
                [
                    'agent_id'            => $singleCallData['agent_id'],
                    'chat_status'          => $singleCallData['chat_status'],
                    'phone_mode'   => $singleCallData['phone_mode'],
                    'phone_status'   => $singleCallData['phone_status'],
                    'mail_status'   => $singleCallData['mail_status'],
                    'presence_status'   => $singleCallData['presence_status'],
                    'status'   => $singleCallData['status'],
                    'request_at'   => $singleCallData['request_at'],
                    'created_at'   => (new DateTime())->getDateTime() //date( 'Y-m-d H:i:s'),
                ]
            );
        }
    }

    /**
     * Insert Multiple User Data to DB
     * @param $multipleUserData
     */
    public function insertMultipleData($multipleUserData): void
    {
        foreach ($multipleUserData as $singleUserData) {
            $this->insertSingleData($singleUserData);
        }
    }

    public function insert($userData): void
    {
        if(empty($userData)){
            return;
        }
        if ( $this->isMultipleArray($userData) ) {
            $this->insertMultipleData($userData);
        } else {
            $this->insertSingleData($userData);
        }
    }

    public function validateBeforeInsert( $callData ): bool
    {
        $validator = \Validator::make(
            [
                'agent_id'      => $callData['agent_id'],
                'chat_status'          => $callData['chat_status'],
                'phone_mode'   => $callData['phone_mode'],
                'phone_status'   => $callData['phone_status'],
                'mail_status'   => $callData['mail_status'],
                'presence_status'   => $callData['presence_status'],
                'status'   => $callData['status'],
                'request_at'   => $callData['request_at'],
                //'created_at'   => $callData['created_at']
            ],
            [
                'agent_id'            => 'required|digits_between:10,19', // required|string|unique:report_missed_calls|
                'chat_status'          => 'max:100',
                'phone_mode'          => 'max:100',
                'phone_status'          => 'max:100',
                'mail_status'          => 'max:100',
                'presence_status'          => 'max:100',
                'status'          => 'max:100',
                'request_at' => 'date_format:Y-m-d H:i:s',
                //'created_at' => 'date_format:Y-m-d H:i:s',
            ]
        );
        if($validator->fails()){
            print_r($validator->errors());exit();
        }
        return ! $validator->fails();
    }


    /**
     * Accordingly cron work I stark this function twice per execution.
     * Totally twice a minute.
     * @throws \Exception
     */
    public function fillTable()
    {
        if(!self::$is_in_process)
        {
            self::$is_in_process = true;
            $o_av = new agentAvailability();
            $a_agent_status = $o_av->getAllAgentsStatuses();
            $this->insert($a_agent_status);
            sleep(29);
            $a_agent_status = $o_av->getAllAgentsStatuses();
            $this->insert($a_agent_status);
            self::$is_in_process = false;
        }
    }

    public function deleteProcessed()
    {
        //\DB::table(self::TABLE_NAME)->where('processed', true)->where('created_at', '<', date('Y-m-d H:i:s', time()-86400) )->delete();
    }
}
