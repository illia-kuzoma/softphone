<?php
namespace App\Zoho\V1;

class UnattendedCalls extends ZohoV1
{

    private $time_to;
    private $time_from;


    private function getTimeFormate($time)
    {
        return date("Y-m-d", $time) . 'T' . date('H:i:s', $time) . '.0Z';
    }

    public function __construct($time_from = null, $time_to = null)
    {
        $time_to = $time_to ?? $this->getTimeFormate(time());
        $time_from = $time_from ?? $this->getTimeFormate(strtotime($time_to) - 86400 * 365);
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
     * @return array
     */
    public function getAUnattended(): array
    {
        return $this->a_unattended;
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
        $a_grouped_by_agents = $this->getAllCount($this->getOrgId(),'2010-02-14T00:00:00.0Z','2020-03-13T09:31:32.0Z',self::FROM,self::LIMIT);
        $users_agent = [];
        foreach($a_grouped_by_agents as $datum){
            $users_agent[] = [
                // 'email' - TODO get email from Accounts/{user_id}
                'first_name' => $datum['agent']['firstName'],
                'last_name' => $datum['agent']['lastName'],
                'photo' => $datum['agent']['photoURL'],
                'role' => 'agent',
                'unattended_count' => $datum['count'],
                'user_id' => $datum['agent']['id'],
            ];
        }
        return $users_agent;
    }

    public function setDataByAgent($agent_id)
    {
        $this->requestUnattendedByAgent($agent_id);
    }

    private function requestUnattendedByAgent($agent_id, $from = self::FROM)
    {
        $unattended_agent = $this->getAll($this->getOrgId(), $agent_id, $this->time_from, $this->time_to, $from,self::LIMIT);
        $this->parseUnattendedByAgent($unattended_agent, $agent_id, $from);
    }

    private function parseUnattendedByAgent($unattended_agent, $agent_id, $from)
    {
        foreach ($unattended_agent as $datum)
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
                'priority' => 'low',
                'phone' => preg_replace("/[^0-9]/", '', $datum['calledFrom']),
                'time_start' => $datum['callTime'],
            ];

            $this->a_users_client[] = [
                // 'email' - TODO get email from Accounts/{user_id}
                'first_name' => $datum['contact']['firstName'],
                'last_name' =>  $datum['contact']['lastName'],
                'photo' => '',
                'role' => 'user',
                'user_id' => $datum['contact']['id'],
            ];
        }
#echo 'unatt= '.count($this->a_unattended) . ' cli=' .  count($this->a_users_client) . "\n";
        if(count($unattended_agent) >= self::LIMIT)
        {
            $this->requestUnattendedByAgent($agent_id, $from + self::LIMIT);
        }
    }
}
