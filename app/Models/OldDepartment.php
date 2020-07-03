<?php


namespace App\Models;


class OldDepartment
{
    private $a_agents_data = [];

    /**
     * @return array
     */
    public function getAAgentsData(): array
    {
        return $this->a_agents_data;
    }

    /**
     * @param array $a_agents_data
     */
    public function setAAgentsData(array $a_agents_data): void
    {
        $this->a_agents_data = $a_agents_data;
    }
    /**
     * Returns agent ids accordingly them department and team
     *
     * @param array $a_department_id
     * @param array $a_team_id
     * @return array
     */
    public function getUserIds($department_id = [], $team_id = [], $uid = [])
    {
        $a_agent_id = $this->getIdsAsArray($uid);
        if(empty($a_agent_id))
        { // If uids not setted then take them from department and teams if them sets.
            $a_department_id = $this->getIdsAsArray($department_id);
            if($a_department_id)
            {
                $a_team_id = $this->getIdsAsArray($team_id);
                foreach($a_department_id as $department_id){
                    $a_teams = (new \App\Zoho\V1\Department())->getAllTeamDataArr($department_id);
                    foreach($a_teams as $team){
                        if((!$a_team_id || (!empty($team['id']) && in_array($team['id'], $a_team_id))) && !empty($team['agents']))
                            foreach($team['agents'] as $agent_id){
                                $a_agent_id[$agent_id] = [
                                    'department' => [
                                        'id' => $department_id,
                                    ],
                                    'team' => [
                                        'id' => $team['id'],
                                    ]
                                ];
                            }
                    }
                }
            }
            if($a_agent_id)
            {
                $this->setAAgentsData($a_agent_id);
                return array_keys($a_agent_id);
            }
        }

        return $a_agent_id;
    }


    public function getTeams($department_id = [])
    {
        $a_teams_return = [];
        $a_department_id = $this->getIdsAsArray($department_id);
        if($a_department_id){
            foreach($a_department_id as $department_id){
                $a_teams = (new \App\Zoho\V1\Department())->getAllTeamDataArr($department_id);
                foreach($a_teams as $team){
                    $a_teams_return[] = [
                        'value' => $team['id'],
                        'name' => $team['name'],
                    ];
                }
            }
        }
        return $a_teams_return;
    }
}
