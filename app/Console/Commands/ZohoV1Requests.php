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

    private function getUnattendedCalls($org_id)
    {
        $a_grouped_by_agents = $this->getUnattendedCallsCount($org_id);
        $agent_id = $this->getRandomAgentId($a_grouped_by_agents);
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
        $result = $this->getUnattendedCalls($org_id);
        print_r($result);
    }
}
