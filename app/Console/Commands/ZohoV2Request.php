<?php

namespace App\Console\Commands;

use App\Models\Glob\DateTime;
use App\Models\ReportAgentTotalStatus;
use App\Models\User;
use App\Zoho\Auth;
use App\Zoho\Config;
use App\Zoho\V1\ActiveTimers;
use App\Zoho\V1\Agent;
use App\Zoho\V1\agentAvailability;
use Illuminate\Console\Command;

/**
 * Class ZohoRequest for testing 2nd version SDK.
 * @package App\Console\Commands
 */
class ZohoV2Request extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:v2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get data from zoho servers.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        new Config([
            "redirect_uri"=>Auth::redirect_uri,
            "currentUserEmail"=>Auth::userEmail
        ]);
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $a=new Agent();
        print_r($a->getAgentByMail('maria@wellnessliving.com'));exit;

        echo (new DateTime())->getDateTime();exit;
        //102325000124362121(ok) 102325000124350224(ok) 102325000123555435(You are not authorized to access this resource)
       //
        (new ReportAgentTotalStatus())->fillTable();
        exit;
        $a_agent_id = User::getAllAgentIDs();
        foreach($a_agent_id as $agent_id)
        {
            print_r((new Agent())->getOne($agent_id));
        }
        exit;
        $from = 0;
        $limit = 100;
        $o = new ActiveTimers();
        /*$a_agent_call = $o->getAll2('102325000021403205', 'calls', $from, $limit, '2020-01-10T06:00:00.000Z','2020-07-11T20:00:00.000Z');
        print_r($a_agent_call);*/

        //(new Calls())->getOne('102325000123304923');
        //(new Tickets())->getOne('102325000123093540');

        (new agentAvailability())->getOne();
exit;

        $a_agent_id = User::getAllAgentIDs();
        $o = new ActiveTimers();
        $a_call = [];
        file_put_contents(base_path('storage/zoho/agents.txt'), "Agent Name | Call Started | Call Time\n");
        foreach($a_agent_id as $agent_id)
        {
            /*$a_user = (array)\DB::table('users')->where('id', '=', $agent_id)->first();
            echo $a_user['first_name'] . " " . $a_user['last_name'] . " ";*/
            $from = 0;
            $limit = 100;
            do
            {
                $a_agent_call = $o->getAll2($agent_id, 'calls', $from, $limit, '2020-01-10T06:00:00.000Z','2020-07-11T20:00:00.000Z');
                foreach($a_agent_call as $item){

                    $time_create = $item['createdTime'];
                    //$time_spent = $item['secondsSpent']+($item['minutesSpent']*60)+($item['hoursSpent']*3600);
                    $time_spent = $item['hoursSpent'].'h '.$item['minutesSpent'].'m '.$item['secondsSpent'].'s';
                    $a_user = (array)\DB::table('users')->where('id', '=', $agent_id)->first();
                    $text_call = $a_user['first_name'] . " " . $a_user['last_name']. " | ".$time_create. " | ".$time_spent."\n";
                    file_put_contents(base_path('storage/zoho/agents.txt'), $text_call, LOCK_EX|FILE_APPEND);
                }
                $from += $limit;
            }
            while(count($a_agent_call) == $limit);
        }

        $text_call = '';
        foreach($a_call as $agent_id=>$a_item)
        {
            foreach($a_item as $time_create=>$time_spent)
            {
            }
        }


        echo ' ';
    }
}
