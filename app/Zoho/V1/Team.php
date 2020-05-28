<?php
namespace App\Zoho\V1;

class Team extends ZohoV1
{
    private const DATA_FIELD = 'teams';
    protected function checkResponse($data): void
    {
        if (!isset($data[static::DATA_FIELD]))
        {
            throw new \Exception(sprintf("Response structure in %s has error!\n %s", __METHOD__, json_encode($data)));
        }
    }
    public function getAll($department_id)
    {
        if(empty($department_id))
        {
            throw new \Exception(sprintf("Department id cannot be empty!"));
        }
        $a_teams = $this->request(
            "https://desk.zoho.com/api/v1/departments/$department_id/teams", '',
            [
            'orgId:' . ((new Organization())->getIdWellnessliving())
            ]
        );
        if(empty($a_teams))
        {
            throw new \Exception(sprintf("Teams list is empty!"));
        }
        $this->checkResponse($a_teams);
        return $a_teams;
    }

    public function getOne($team_id)
    {
        if(empty($team_id))
        {
            throw new \Exception(sprintf("Department id cannot be empty!"));
        }
        $a_team = $this->request(
            "https://desk.zoho.com/api/v1/teams/$team_id", '',
            [
                'orgId:' . ((new Organization())->getIdWellnessliving())
            ]
        );
        if(empty($a_team))
        {
            throw new \Exception(sprintf("Team list is empty!"));
        }
        return $a_team;
    }

    public function getAllDataArr($department_id)
    {
        $a_return = [];
        $a_teams = $this->getAll($department_id)[static::DATA_FIELD];
        foreach($a_teams as $team){
            $a_return[] = [
                'id' => $team['id'],
                'name' => $team['name'],
                'agents' => $team['agents'],
                'description' => $team['description'],
                'department_id' => $team['departmentId']
            ];
        }
        return $a_return;
    }

    public function getTeamDataArr($team_id)
    {
        $a_return = [];
        $a_team = $this->getOne($team_id);
        $a_return[] = [
            'id' => $a_team['id'],
            'name' => $a_team['name'],
            'agents' => $a_team['agents'],
            'description' => $a_team['description']
        ];
        return $a_return;
    }
}
