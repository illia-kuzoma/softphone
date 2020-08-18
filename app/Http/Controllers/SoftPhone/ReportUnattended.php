<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Response;
use App\Http\Controllers\Traits\UserAuth;
use App\Models\Team;
use App\Models\User;

class ReportUnattended extends Controller
{
    use UserAuth;
    use Response;

    private const DIAGRAM_DATA = 'diagrama';
    private const CALLS_DATA = 'calls';
    private const TEAM_IDS = 'teams';

    private function _getAll($token, $dateStart=null, $period=null, $uids=null, $refresh = false): string
    {
        $user = $this->getUser($token);
        if($user instanceof User)
        {
            $a_agent_id = [];

            $unattendedCalls = new \App\Models\ReportUnattended($a_agent_id);

            if($refresh) // Подтянуть данные с Зохо.
                $unattendedCalls->loadFromRemoteServer();

            $out = array_merge($this->getResponse($user), [
                self::CALLS_DATA => $unattendedCalls->getCallList($dateStart, $period, null,null,null,null),
                self::DIAGRAM_DATA => $unattendedCalls->getDiagramList($dateStart, $period),
            ]);
            return json_encode($out);
        }
        return $user;
    }

    /**
     * @param $token
     * @param null $dateStart
     * @param null $period
     * @param null $uids
     * @return string
     * @throws \Exception
     */
    public function getAll($token, $dateStart=null, $period=null, $uids=null): string
    {
        return $this->_getAll($token, $dateStart, $period, $uids);
    }

    /**
     * @param null $dateStart
     * @param null $period
     * @param null $departments
     * @param null $teams
     * @param null $uid
     * @param null $searchWord
     * @param null $sortField
     * @param string $sortBy
     * @param int $page
     * @return string
     */
    public function getCalls($dateStart=null, $period=null, $departments=null, $teams=null, $uid=null, $searchWord=null, $sortField=null, $sortBy='DESC', $page = 1): string
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

        $unattendedCalls = new \App\Models\ReportUnattended($a_agent_id);
        $calls = $unattendedCalls->getCallList($dateStart, $period, $searchWord, $sortField, $sortBy, $page);

        $out = array_merge([
            self::DIAGRAM_DATA => $unattendedCalls->getDiagramList($dateStart, $period),
            self::CALLS_DATA => $calls,
            self::TEAM_IDS => $a_team
        ], $this->getAgentsArr());
        return json_encode($out);
    }

    private function _getAgentIds()
    {

    }

    /**
     * @param $token
     * @param null $dateStart
     * @param null $period
     * @param null $uids
     * @return string
     * @throws \Exception
     */
    public function getFreshData($token, $dateStart=null, $period=null, $uids=null): string
    {
        return $this->_getAll($token, $dateStart, $period, $uids, true);
    }


    private function _getIdsAsArray($data)
    {
        $a_id = [];

        if($data == '-' || !$data)
        {

        }
        elseif(is_string($data))
        {
            $a_id = explode(',', $data);
            if(count($a_id) > 1)
            {
                // Значит фильтруем по несольким юзерам
            }
            elseif(count($a_id) == 1){
                $a_id = [(int)$a_id[0]];
            }
        }
        elseif(is_int($data))
            $a_id = [$data];
        elseif(is_array($data))
            return $data;

        return $a_id;
    }
}
