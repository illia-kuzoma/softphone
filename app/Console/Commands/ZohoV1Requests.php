<?php

namespace App\Console\Commands;

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
        $org_id = (new Organization())->getIdWellnessliving();
        $zo = new UnattendedCalls();
        $a_grouped_by_agents = $zo->getAllCount($org_id,'2010-02-14T00:00:00.187Z','2020-03-13T09:31:32.187Z',1,99);
        print_r($a_grouped_by_agents);
        exit;

        $users_agents = [];
        $agents_grouped_unattended = [];
        foreach($a_grouped_by_agents['data'] as $datum){
            $users_agents[] = [
                'id' => $datum['agent']['id'],
                'photoURL' => $datum['agent']['photoURL'],
                'firstName' => $datum['agent']['firstName'],
                'lastName' => $datum['agent']['lastName'],
                'unattended_count' => $datum['count'],
            ];
            /*for($i=0; $i < $datum['count'];++$i){
                $agents_grouped_unattended[$i] = [
                    'id' => $datum['agent']['id'],
                    'photoURL' => $datum['agent']['photoURL'],
                    'firstName' => $datum['agent']['firstName'],
                    'lastName' => $datum['agent']['lastName'],
                ];
            }*/
        }
        //print_r($agents_grouped_unattended);
        print_r($users_agents); // TODO  покаждому юзеру делать запрос на пропущенные.


        #print_r($a_grouped_by_agents);
        $agent_id = $this->getRandomAgentId($a_grouped_by_agents);
        $result = $this->getUnattendedCalls($org_id, $agent_id);
        print_r($result);


        $zo = new UnattendedCalls();
        foreach($users_agents as $user)
        {
            $agent_id = $user['id'];
            $res = $zo->getAll($org_id, $agent_id,'2019-03-14T00:00:00.187Z','2020-03-13T09:31:32.187Z',1,99);
            var_dump($res);
        }
    }
}
