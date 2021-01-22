<?php

namespace App\Console\Commands;

use App\Models\ReportUnattendedCall;
use App\Models\ReportUnattendedGraph;
use App\Models\User;
use App\Zoho\V1\Organization;
use App\Zoho\V1\UnattendedCalls;
use App\Zoho\V2\Contacts;
use Illuminate\Console\Command;
use zcrmsdk\crm\utility\ZCRMConfigUtil;

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
        $this->zo = new UnattendedCalls();
        $users_agent = $this->zo->getAgentsList();
        print_R($users_agent);
        exit;
        print_r((new Contacts())->searchInContactsByEmail("fabianaclaure@gmail.com"));
        exit;
        $res = exec('curl "https://www.zohoapis.com/crm/v2/Contacts/search?phone=905-447-3933" -H "Authorization: Zoho-oauthtoken '.ZCRMConfigUtil::getAccessToken().'" -X GET');
print_r($res);exit;

       /* $t = (new AuthByPassword())->getToken(null,null); // ZCRMConfigUtil::getAccessToken()

        $url = "https://crm.zoho.com/crm/private/json/Calls/getRecords";
        $param= "authtoken=".$t."&scope=crmapi";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result;
        exit();


        exit();*/

        $res = exec('curl "https://www.zohoapis.com/crm/v2/Contacts/search?word=Believe+Yoga" -H "Authorization: Zoho-oauthtoken '.ZCRMConfigUtil::getAccessToken().'" -X GET');

        echo $res;exit;

        $res = exec('curl "https://www.zohoapis.com/crm/v2/coql" -H "Authorization: Zoho-oauthtoken '.ZCRMConfigUtil::getAccessToken().'" -d "@/var/www/softphone/app/Console/Commands/input.json" -X POST');

        echo $res;exit;
        $res = exec('curl "https://www.zohoapis.com/crm/v2/Contacts/search?phone=17789087037" -H "Authorization: Zoho-oauthtoken '.ZCRMConfigUtil::getAccessToken().'" -X GET');

        /*Industry
        Business_Link
        Business_Group
        "Account_Name":{
        "name":"Believe Yoga",
            "id":"1602133000026462291"

        }
        "Business_ID":"82456",
                 "Legal_Business_Name":"NAMASTACYS KICK ASS YOGA",


         "Business_Group":"Yoga and Pilates",
        */

        echo $res;exit;
       $res = exec('curl "https://www.zohoapis.com/crm/v2/coql" -H "Authorization: Zoho-oauthtoken '.ZCRMConfigUtil::getAccessToken().'" -d "@/var/www/softphone/app/Console/Commands/input.json" -X POST');
       echo $res;exit;
        $a_headers = $a_result = [];
        $s_param = '';
        $d =
        $data_string =
        $s_url = "https://www.zohoapis.com/crm/v2/coql";
        $ch = curl_init($s_url /*. '?' . $s_param*/);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization:Zoho-oauthtoken ' . ZCRMConfigUtil::getAccessToken()]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $s_result = curl_exec($ch);
        /*if(($s_result = curl_exec($ch)) === false)
        {
            Log::put(sprintf("Error curl: %s", curl_error($ch)));
            throw new \Exception(sprintf("Response in %s has error!\n %s\n code err is %d\n",
                __METHOD__, curl_error($ch), curl_error($ch)));
        }
        else
        {
            // Log::put(sprintf("curl_exec %s", $s_result));
            $a_result = json_decode($s_result, true);
        }
        print_r($s_result);*/
        curl_close($ch);
        print_r($s_result);

        exit;

     /*   $unattendedCalls = new ReportUnattendedCall();
        $unattendedCalls->loadFromRemoteServer();
        exit;
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
