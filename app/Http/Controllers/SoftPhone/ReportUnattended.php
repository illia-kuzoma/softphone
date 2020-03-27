<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Models\User;

class ReportUnattended extends Controller
{
    private const DIAGRAM_DATA = 'diagrama';
    private const CALLS_DATA = 'calls';
    private const USER_DATA = 'user';
    private const AGENT_IDS = 'agents';

    /**
     * @param null $dateStart
     * @param null $period
     * @return false|string
     */
    public function getAll($dateStart=null, $period=null, $uids=null): string
    {
        $user = new User();
        $user->getData($email = 'support@wellnessliving.com');
        if(!$user->checkToken()){
            return redirect()->action(
                'SoftPhone\Auth@getAuth', ['message' => "Please enter to system."]
            );
        }
        $a_agent_id = [];
        if(is_string($uids))
            $a_agent_id = explode(',', $uids);

        $unattendedCalls = new \App\Models\ReportUnattended($a_agent_id);
        $unattendedCalls->loadFromRemoteServer();

        $out = [
            self::CALLS_DATA => $unattendedCalls->getCallList($dateStart, $period, null,null,null,null),
            self::DIAGRAM_DATA => $unattendedCalls->getDiagramList($dateStart, $period),
            self::USER_DATA => $user->toArray(),
            self::AGENT_IDS => User::getAllAgentIDFullName(),
        ];

        return json_encode($out);
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
    public function getCalls($dateStart=null, $period=null, $uid=null, $searchWord=null, $sortField=null, $sortBy='DESC', $page = 1): string
    {
        $a_agent_id = [];

        if($uid == '-' || !$uid){
            $a_agent_id = [];
        }
        elseif(is_string($uid))
        {
            $a_agent_id = explode(',', $uid);
            if(count($a_agent_id) > 1)
            {
                // Значит фильтруем по несольким юзерам
            }
            elseif(count($a_agent_id) == 1){
                $a_agent_id = [(int)$a_agent_id[0]];
            }
        }
        elseif(is_int($uid))
            $a_agent_id = [$uid];

        $unattendedCalls = new \App\Models\ReportUnattended($a_agent_id);
        $unattendedCalls->loadFromRemoteServer();

        $out = [
            self::DIAGRAM_DATA => $unattendedCalls->getDiagramList($dateStart, $period),
            self::CALLS_DATA => $unattendedCalls->getCallList($dateStart, $period, $searchWord, $sortField, $sortBy, $page)
        ];
        return json_encode($out);
    }
}
