<?php


namespace App\Http\Controllers\Traits;

use App\Models\Team;
use App\Models\User;

trait Response
{

    /**
     * Merges common response data with unique ones.
     *
     * @param User $user
     * @return array
     */
    public function getResponse(User $user, array $a_agent_id = [])
    {
        return array_merge(
            [
                'user' => $user->toArray(),
                'token' => $user->getToken(),
                'departments' => (new \App\Models\Department())->getAllArr()
            ],$this->getAgentsArr($a_agent_id)
        );
    }

    public function getAgentsArr($a_agent_id = [])
    {
        return [
            'agents' => User::getAllAgentIDFullName($a_agent_id),
        ];
    }

    public function getTeamAndDepartmentList($a_department_id, $a_team_id, $a_agent_id)
    {
        $o_user = new User();
        // Получаю список агентов согласно департментам и командам.
        $a_agent_id_by_teams = $o_user->getIdArrByTeams($a_department_id, $a_team_id);


        $o_team = new Team();
        if(empty($a_agent_id)) // Если агенты не указаны, тогда беру их согласно указанным отделам и командам.
        {
            $a_agent_id = $a_agent_id_by_teams;
            $a_team = $o_team->getAllArr($a_department_id, $a_team_id);
            $a_department = (new \App\Models\Department())->getAllArr();
        }
        else{
            list($a_department, $a_team) = $o_user->getTeamAndDepartmentList($a_agent_id);

            if($a_department && $a_team)
                $a_team = $o_team->getAllArr($a_department, $a_team);
            if($a_department)
                $a_department = (new \App\Models\Department())->getAllArr($a_department);
        }
        return [$a_department, $a_team, $a_agent_id];
    }
}
