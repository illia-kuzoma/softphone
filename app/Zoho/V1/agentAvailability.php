<?php


namespace App\Zoho\V1;


class agentAvailability extends ZohoV1
{

    const FROM = 0;
    const LIMIT = 20;

    public function getAll($from=0, $limit=20): array
    {
        $a_results = $this->request(
            "https://desk.zoho.com/api/v1/agentAvailability", 'departmentId=-1&include=mailStatus,phoneStatus,chatStatus,phoneMode,presenceStatus&from='.$from.'&limit='.$limit,
            [
                'orgId:' . ((new Organization())->getIdWellnessliving())
            ]
        );
        if(empty($a_results))
        {
            throw new \Exception(sprintf("Departments list is empty!"));
        }
        $this->checkResponse($a_results);
        return $a_results;
    }

    /**
     * @return array all agents statuses for request time.
     * @throws \Exception
     */
    public function getAllAgentsStatuses()
    {
        $from = self::FROM;
        $limit = self::LIMIT;
        $a_agent_status = [];
        do
        {
            $request_time = date('Y-m-d H:i:s');
            $a_data = $this->getAll($from, $limit);
            //echo count($a_data['data'])." -- ";
            foreach($a_data['data'] as $item){

                $a_agent_status[] = [
                    'agent_id' => $item['agentId'],
                    'chat_status' => $item['chatStatus']??'',
                    'phone_mode' => $item['phoneMode']??'',
                    'phone_status' => $item['phoneStatus']??'',
                    'mail_status' => $item['mailStatus']??'',
                    'presence_status' => $item['presenceStatus']??'',
                    'status' => $item['status']??'',
                    'request_at' => $request_time,
                ];
            }
            $from += $limit;
            //echo count($a_agent_status) .'<'. $a_data['totalAgentsCount'].'   '.$a_data['hasMore'] ."\n";
        }
        while(count($a_agent_status) < $a_data['totalAgentsCount'] || $a_data['hasMore'] == '1');
        //echo count($a_agent_status);exit;
        return $a_agent_status;
    }
}
