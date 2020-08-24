<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Request;
use App\Http\Controllers\Traits\Response;
use App\Http\Controllers\Traits\UserAuth;
use App\Models\Team;
use App\Models\User;

/**
 * Class ReportAgentStatus use to get agent/agents statuses in time line.
 * @package App\Http\Controllers\SoftPhone
 */
class ReportAgentStatus extends Controller
{
    use UserAuth;
    use Response;
    use Request;

    private const DIAGRAM_DATA = 'diagrama';
    private const STATUS_DATA = 'status';
    private const TEAM_IDS = 'teams';

    private function _getAll($token, $dateStart=null, $period=null, $uids=null, $refresh = false): string
    {
        $user = $this->getUser($token);
        if($user instanceof User)
        {
            $a_agent_id = [];

            $o_statuses = new \App\Models\ReportAgentStatuses($a_agent_id);

            $out = array_merge($this->getResponse($user), [
                self::STATUS_DATA => $o_statuses->getStatusList($dateStart, $period, null,null,null,null),
                self::DIAGRAM_DATA => [],
            ]);

            return json_encode($out);
        }
        return $user;
    }
    //
    public function getAll($token, $dateStart=null, $period=null, $uids=null): string
    {
        return $this->_getAll($token, $dateStart, $period, $uids);
    }

    public function getPage($dateStart=null, $period=null, $departments=null, $teams=null, $uid=null, $searchWord=null, $sortField=null, $sortBy='DESC', $page = 1): string
    {
        $a_department_id = $this->_getIdsAsArray($departments);
        $a_team_id = $this->_getIdsAsArray($teams);
        $a_agent_id = $this->_getIdsAsArray($uid);
        $o_user = new User();
        // Получаю список агентов согласно департментам и командам.
        $a_agent_id_by_teams = $o_user->getIdArrByTeams($a_department_id, $a_team_id);

        if(empty($a_agent_id)) // Если агенты не указаны, тогда беру их согласно указанным отделам и командам.
            $a_agent_id = $a_agent_id_by_teams;

        $o_team = new Team();
        $a_team = $o_team->getAllArr($a_department_id, $a_team_id);

        $o_statuses = new \App\Models\ReportAgentStatuses($a_agent_id);

        $out = array_merge([
            self::DIAGRAM_DATA => [],
            self::STATUS_DATA => $o_statuses->getStatusList($dateStart, $period, $searchWord, $sortField, $sortBy, $page),
            self::TEAM_IDS => $a_team
        ], $this->getAgentsArr());
        return json_encode($out);
    }
}
