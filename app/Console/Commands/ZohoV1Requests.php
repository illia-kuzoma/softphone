<?php

namespace App\Console\Commands;

use App\Models\ReportUnattendedCall;
use App\Models\ReportUnattendedGraph;
use App\Models\User;
use App\Zoho\V1\Organization;
use App\Zoho\V1\UnattendedCalls;
use Illuminate\Console\Command;

/**
 * Class ZohoV1Requests for testing 1st version SDK.
 * @package App\Console\Commands
 */
class ZohoV1Requests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:v1';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private $org_id;
    /**
     * @var array
     */
    private $users_client = [];
    private $users_client_id = [];
    /**
     * @var UnattendedCalls
     */
    private $zo;
    /**
     * @var array
     */
    private $a_unattended;

    /**
     * @return mixed
     */
    public function getOrgId()
    {
        return $this->org_id?$this->org_id:$this->pullOrgId();
    }

    /**
     * @param mixed $org_id
     */
    public function setOrgId($org_id): void
    {
        $this->org_id = $org_id;
    }
    public function pullOrgId()
    {
        $this->setOrgId((new Organization())->getIdWellnessliving());
        return $this->getOrgId();
    }

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function getRandomAgentId($a_grouped_by_agents)
    {
        if(!empty($a_grouped_by_agents['data']))
        {
            $data = $a_grouped_by_agents['data'];
            return $data[mt_rand(0, count($data)-1)]['agent']['id'];
        }
        throw new \Exception(sprintf("Not correct structure of data grouped by agents."));
    }

    private function getUnattendedCalls($org_id, $agent_id)
    {
        $zo = new UnattendedCalls();
        return $zo->getAll($org_id, $agent_id);
    }

    private function getUnattendedCallsCount($org_id):array
    {
        $zo = new UnattendedCalls();
        return $zo->getAllCount($org_id);
    }

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle()
    {

     /*   $unattendedCalls = new ReportUnattendedCall();
        $unattendedCalls->loadFromRemoteServer();
        exit;*/
        /*$unattendedCalls = new ReportUnattendedCall();
        $unattendedCalls->loadFromRemoteServer();
        exit;*/
        //////////////////////////////////////////////
        ///////////Get agents list////////
        //////////////////////////////////////////////
        $this->zo = new UnattendedCalls();
        $users_agent = $this->zo->getAgentsList();

        $o_users = new User();
        $o_users->insert($users_agent);
        print_r($users_agent);exit;
        $this->parseUnattended($users_agent);
        $o_ug = new ReportUnattendedGraph();
        $o_ug->updateDB();
    }

    private function parseUnattended($users_agent)
    {
        $o_users = new User();
        $this->users_client;
        $o_u = new ReportUnattendedCall();

        foreach($users_agent as $user)
        {
            $this->requestUnattendedByAgent($user['user_id']);
            $o_u->insert($this->a_unattended);
            $this->a_unattended = [];
            $o_users->insert($this->users_client);
            $this->users_client = [];
        }
    }
    private function requestUnattendedByAgent($agent_id, $from = self::FROM)
    {
        $s_now = $this->getTimeFormate(time());
        $s_year_below = $this->getTimeFormate(strtotime($s_now) - 86400*365);
        $res = $this->zo->getAll($this->getOrgId(), $agent_id,$s_year_below,$s_now, $from,self::LIMIT);
        $this->parseUnattendedByAgent($res, $agent_id, $from);
    }

    private function parseUnattendedByAgent($unattended_agent, $agent_id, $from)
    {
        if(isset($unattended_agent['data']))
        {
            foreach ($unattended_agent['data'] as $datum)
            {
                $this->a_unattended[] = [
                    'id' => $datum['call']['id'],
                    'agent_id' => $agent_id,
                    'user_id' => $datum['contact']['id'],
                    'business_name' => '',
                    'contact' => json_encode([
                        'name' => $datum['contact']['firstName'] .' '. $datum['contact']['lastName'],
                        'phone' => $datum['contact']['phone'],
                        'email' =>  $datum['contact']['email'],
                    ]),
                    'priority' => ReportUnattendedCall::PRIORITY_LOW,
                    'phone' => preg_replace("/[^0-9]/", '', $datum['calledFrom']),
                    'time_start' => $datum['callTime'],
                ];

                $this->users_client[] = [
                    // 'email' - TODO get email from Accounts/{user_id}
                    'first_name' => $datum['contact']['firstName'],
                    'last_name' =>  $datum['contact']['lastName'],
                    'photo' => '',
                    'role' => 'user',
                    'user_id' => $datum['contact']['id'],
                ];
            }

            if(count($unattended_agent['data']) >= self::LIMIT)
            {
                $this->requestUnattendedByAgent($agent_id, $from+self::LIMIT);
            }
        }
    }

    private function getTimeFormate($time)
    {
        return date("Y-m-d", $time).'T'.date('H:i:s', $time).'.0Z';
    }
}
