<?php


namespace App\Zoho\V1;


class Agent extends ZohoV1
{
    private $a_agent = [];
    public function getOne($agent_id):void
    {
        $a_results = $this->request(
            "https://desk.zoho.com/api/v1/agents/$agent_id", 'include=role,associatedDepartments,associatedChatDepartments',
            [
                'orgId:' . ((new Organization())->getIdWellnessliving())
            ]
        );
        if(empty($a_results))
        {
            throw new \Exception(sprintf("Agent data is empty!"));
        }
        $this->a_agent = $a_results;
    }

    public function getAgentMail(): string
    {
        $a_agent = $this->a_agent;
        return $a_agent['emailId'];
    }

    public function getAgentRole(): string
    {
        $a_agent = $this->a_agent;
        if(empty($a_agent['role']))
        {
            return '';
        }
        return $a_agent['role']['name'];
    }

    public function getAgentStatus(): string
    {
        $a_agent = $this->a_agent;
        return $a_agent['status'];
    }

    public function getAll($from, $limit)
    {
        $a_results = $this->request(
            "https://desk.zoho.com/api/v1/agents", 'limit='.$limit.'&from='.$from.'',
            [
                'orgId:' . ((new Organization())->getIdWellnessliving())
            ]
        );
        if(empty($a_results))
        {
            throw new \Exception(sprintf("Agents data is empty!"));
        }
        return $a_results;
    }

    public function getAgentByMail($email,$from = 0,$limit = 200): ?array
    {
        $a_results = $this->getAll($from, $limit);
        foreach($a_results['data'] as $a_result)
        {
            if(!isset($a_result['emailId']))
            {
                continue;
            }

            if($a_result['emailId'] == $email)
            {
                return $a_result;
            }
        }
        $i = 0;
        if(count($a_results['data']) == $limit)
        {
            $from += $limit;
            if(++$i == 100)
            {
                echo "Error agents cnt";return null;
            }
            $this->getAgentByMail($email, $from, $limit);
        }
        return null;
    }
}
