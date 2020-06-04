<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;

class ReportUnattended extends Controller
{
    private const DIAGRAM_DATA = 'diagrama';
    private const CALLS_DATA = 'calls';
    private const USER_DATA = 'user';
    private const AGENT_IDS = 'agents';

    private function _getAll($token, $dateStart=null, $period=null, $uids=null, $refresh = false): string
    {
        try{
            $user = new User();
            $user->getUserByToken($token);
            if(!$user->checkToken()){
                return redirect()->action(
                    'SoftPhone\Auth@getAuth', ['message' => "Please enter to system."]
                );
            }
        }
        catch(\Exception $e)
        {
            return redirect()->action(
                'SoftPhone\Auth@getAuth', ['message' => $e->getMessage()]
            );
        }
        $a_agent_id = [];
        /*if(is_string($uids))
            $a_agent_id = explode(',', $uids);*/

        $unattendedCalls = new \App\Models\ReportUnattended($a_agent_id);

        if($refresh)
            $unattendedCalls->loadFromRemoteServer();

        $out = [
            self::CALLS_DATA => $unattendedCalls->getCallList($dateStart, $period, null,null,null,null),
            self::DIAGRAM_DATA => $unattendedCalls->getDiagramList($dateStart, $period),
            self::USER_DATA => $user->toArray(),
            self::AGENT_IDS => User::getAllAgentIDFullName(),
            'token' => $user->getToken(),
            'departments' => (new \App\Models\Department())->getAllArr()
        ];

        return json_encode($out);
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
     * @param null $uids
     * @param null $searchWord
     * @param null $sortField
     * @param string $sortBy
     * @param int $page
     *
     * @return string
     */
    public function getCalls($dateStart=null, $period=null, $departments=null, $teams=null, $uid=null, $searchWord=null, $sortField=null, $sortBy='DESC', $page = 1): string
    {
        $a_department_id = $this->_getIdsAsArray($departments);
        $a_team_id = $this->_getIdsAsArray($teams);
        $a_agent_id = $this->_getIdsAsArray($uid);
        $o_user = new User();
        $a_agent_id_by_teams = $o_user->getIdArrByTeams($a_department_id, $a_team_id);
        if(empty($a_agent_id))
        {
            $a_agent_id = $a_agent_id_by_teams;
        }
        $o_team = new Team();
        $a_team = $o_team->getAllArr($a_department_id, $a_team_id);

       /* $o_department = new \App\Models\Department();
        $a_agent_id = $o_department->getUserIds($departments, $teams, $uid);
        $a_agent_data = $o_department->getAAgentsData();
        $unattendedCalls = new \App\Models\ReportUnattended($a_agent_id);

        //$unattendedCalls->loadFromRemoteServer();
        $a_department = (new Department())->getDataArr();
        $a_team = $o_department->getTeams($departments);*/
        $unattendedCalls = new \App\Models\ReportUnattended($a_agent_id);
        $calls = $unattendedCalls->getCallList($dateStart, $period, $searchWord, $sortField, $sortBy, $page);

        /*if(empty($a_agent_data))
        {
            $a_department_id = array_column($a_department, 'value');
            $o_department->getUserIds($a_department_id);
            $a_agent_data = $o_department->getAAgentsData();
            $a_team = $o_department->getTeams($a_department_id);
        }
        foreach($calls['data'] as &$call)
        {
            $uid = $call['uid'];
            $department_id = !empty($a_agent_data[$uid])?$a_agent_data[$uid]['department']['id']:null;
            $team_id = !empty($a_agent_data[$uid])?$a_agent_data[$uid]['team']['id']:null;

            $call['user_data']['department']['id'] = 0;
            $call['user_data']['department']['name'] = '-';
            $call['user_data']['team']['id'] = 0;
            $call['user_data']['team']['name'] = '-';
            foreach($a_department as $department){
                if($department['value'] == $department_id)
                {
                    $call['user_data']['department']['id'] = $department_id;
                    $call['user_data']['department']['name'] = $department['name'];
                    break;
                }
            }
            foreach($a_team as $team){
                if($team['value'] == $team_id)
                {
                    $call['user_data']['team']['id'] = $team_id;
                    $call['user_data']['team']['name'] = $team['name'];
                    break;
                }
            }
        }
        unset($call);*/
        $out = [
            self::DIAGRAM_DATA => $unattendedCalls->getDiagramList($dateStart, $period),
            self::CALLS_DATA => $calls,
            self::AGENT_IDS => User::getAllAgentIDFullName($a_agent_id_by_teams),
            'teams' => $a_team
        ];
        return json_encode($out);
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
