<?php
namespace App\Zoho\V1;

use App\Models\User;
use App\Zoho\V2\Contacts;

class UnattendedCalls extends ZohoV1
{

    private $time_to;
    private $time_from;

    /**
     * @return string|null
     */
    public function getTimeTo(): ?string
    {
        return $this->time_to;
    }

    /**
     * @return string|null
     */
    public function getTimeFrom(): ?string
    {
        return $this->time_from;
    }


    private function getTimeFormate($time)
    {
        return date("Y-m-d", $time) . 'T' . date('H:i:s', $time) . '.0Z';
    }

    public function __construct($i_time_from = null, $i_time_to = null)
    {
        $i_now = time();
        $time_to = $i_time_to? $this->getTimeFormate($i_time_to): $this->getTimeFormate($i_now);
        $time_from = $i_time_from? $this->getTimeFormate($i_time_from): $this->getTimeFormate($i_now - (86400 * 100));
        $this->time_from = $time_from;
        $this->time_to = $time_to;
    }

    const FROM = 1;
    const LIMIT = 99;
    private $org_id;
    /**
     * @var array
     */
    private $a_unattended = [];
    /**
     * @var array
     */
    private $a_users_client = [];

    /**
     * @param array $a_users_client
     */
    public function setAUsersClient(array $a_users_client): void
    {
        $this->a_users_client = $a_users_client;
    }

    /**
     * @return array
     */
    public function getAUnattended(): array
    {
        return $this->a_unattended;
    }

    /**
     * @param array $a_unattended
     */
    public function setAUnattended(array $a_unattended): void
    {
        $this->a_unattended = $a_unattended;
    }

    /**
     * @return array
     */
    public function getAUsersClient(): array
    {
        return $this->a_users_client;
    }

    public function getOrgId()
    {
        return $this->org_id ? $this->org_id : $this->pullOrgId();
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

    public function getAllCount($org_id, $from_time = '2019-01-01T07:47:43.206Z', $end_time = '2020-02-23T07:47:43.206Z', $from = 1, $limit = 99)
    {
        $res = $this->request(
            "https://desk.zoho.com/api/v1/sequentialUnattendedCalls/Count",
            'fromTime='.$from_time.'&endTime='.$end_time.'&from='.$from.'&limit='.$limit,
            [
                'orgId:' . $org_id
            ]
        );
        $this->checkResponse($res);
        return $res['data'];
    }

    public function getAll($org_id, $assignee_id, $from_time = '2019-10-01T07:47:43.206Z', $end_time = '2020-02-23T07:47:43.206Z', $from = 1, $limit = 99)
    {
        $res = $this->request(
            "https://desk.zoho.com/api/v1/sequentialUnattendedCalls",
            'assigneeId='.$assignee_id.'&fromTime='.$from_time.'&endTime='.$end_time.'&from='.$from.'&limit='.$limit,
            [
                'orgId:' . $org_id
            ]
        );
        $this->checkResponse($res);
        return $res['data'];
    }

    public function getAgentsList(): array
    {
        $users_agent = [];
        $from = self::FROM;
        do{
            $a_grouped_by_agents = $this->getAllCount($this->getOrgId(),'2010-02-14T00:00:00.0Z',$this->getTimeFormate(time()- 10000),$from,self::LIMIT);
            foreach($a_grouped_by_agents as $datum)
            {
                $agent_id = $datum['agent']['id'];
                $a_client_tmp = [
                    // 'email' - TODO get email from Accounts/{user_id}
                    'first_name' => $datum['agent']['firstName'],
                    'last_name' => $datum['agent']['lastName'],
                    'photo' => $datum['agent']['photoURL'],
                    'role' => 'agent',
                    'unattended_count' => $datum['count'],
                    'user_id' => $agent_id,
                ];

                if(isset($this->a_additional_agents_data[$agent_id]))
                {
                    $a_client_tmp['team_id'] = '';
                    if(!empty($this->a_additional_agents_data[$agent_id]['team_id']))
                    {
                        $a_client_tmp['team_id'] = implode(',', array_keys($this->a_additional_agents_data[$agent_id]['team_id']));
                    }
                    $a_client_tmp['department_id'] = $this->a_additional_agents_data[$agent_id]['department_id'];
                }

                $users_agent[] = $a_client_tmp;
            }
            $from += self::LIMIT;
        }while(isset($a_grouped_by_agents[self::LIMIT]));
        return $users_agent;
    }

    public function setDataByAgent($agent_id)
    {
        $this->requestUnattendedByAgent($agent_id);
    }

    private function requestUnattendedByAgent($agent_id, $from = self::FROM)
    {
        $unattended_agent = $this->getAll($this->getOrgId(), $agent_id, $this->time_from, $this->time_to, $from,self::LIMIT);
        //Log::put("getAll: " . json_encode($unattended_agent));
        $this->parseUnattendedByAgent($unattended_agent, $agent_id, $from);
    }

    private function parseUnattendedByAgent($unattended_agent, $agent_id, $from)
    {
        if(empty($unattended_agent))
            return;

        foreach ($unattended_agent as $datum)
        {
            $phone = preg_replace("/[^0-9]/", '', $datum['calledFrom']);
            $contact_name = $datum['contact']['firstName'] .' '. $datum['contact']['lastName'];

            /////// Get business name and link data //////////
            $o_zoho_contacts = new Contacts();
            $a_business = $o_zoho_contacts->searchInContactsByEmail($datum['contact']['email']);
            if(empty($a_business))
                $a_business = $o_zoho_contacts->searchInContactsByPhone($phone);

            if(empty($a_business['name']))
            {
                $a_business['name'] = $contact_name;
            }
            if(empty($a_business['link']))
            {
                $a_business['link'] = null;
            }
            ////////////////////////////////////////////////////

            $this->a_unattended[] = [
                'id' => $datum['call']['id'],
                'agent_id' => $agent_id,
                'user_id' => $datum['contact']['id'],
                'business_name' => json_encode($a_business),
                'contact' => json_encode([
                    'name' => $contact_name,
                    'phone' => $datum['contact']['phone'],
                    'email' =>  $datum['contact']['email'],
                ]),
                'priority' => 'low',
                'phone' => $phone,
                'time_start' => $datum['callTime'],
            ];

            $a_client_tmp = [
                'email' => $datum['contact']['email'],
                'first_name' => $datum['contact']['firstName'],
                'last_name' =>  $datum['contact']['lastName'],
                'photo' => '',
                'role' => User::ROLE_USER,
                'user_id' => $datum['contact']['id'],
            ];
            $this->a_users_client[] = $a_client_tmp;
        }

        if(count($unattended_agent) >= self::LIMIT)
        {
            $this->requestUnattendedByAgent($agent_id, $from + self::LIMIT);
        }
    }

    private $a_additional_agents_data = [];

    /**
     * @return array
     */
    public function getAAdditionalAgentsData(): array
    {
        return $this->a_additional_agents_data;
    }

    /**
     * @param array $a_additional_agents_data
     */
    public function setAAdditionalAgentsData(array $a_additional_agents_data): void
    {
        $this->a_additional_agents_data = $a_additional_agents_data;
    }
}
