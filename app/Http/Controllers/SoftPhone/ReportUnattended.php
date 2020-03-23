<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Models\ReportUnattendedCall;
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
        $a_uid = [];
        if(is_string($uids))
            $a_uid = explode(',', $uids);

        $unattendedCalls = new ReportUnattendedCall();
        $unattendedCalls->setFilterByUsers($a_uid);
        $unattendedCalls->loadFromRemoteServer();

        $out = [
            self::CALLS_DATA => $unattendedCalls->getList(),
            self::DIAGRAM_DATA => $unattendedCalls->getDiagramList($dateStart, $period),
            self::USER_DATA => $user->toArray(),
            self::AGENT_IDS => User::getAllAgentIDFullName(),
        ];

        return json_encode($out);
    }

    /**
     * @param null $dateStart
     * @param null $period
     * @param null $uid
     * @param null $searchWord
     * @param null $sortField
     * @param string $sortBy
     * @param int $page
     *
     * @return string
     */
    public function getCalls($dateStart=null, $period=null, $uid=null, $searchWord=null, $sortField=null, $sortBy='DESC', $page = 1): string
    {
        $unattendedCalls = new ReportUnattendedCall();
        $unattendedCalls->loadFromRemoteServer();

        $out = [
            self::DIAGRAM_DATA => $unattendedCalls->getDiagramList($dateStart, $period),
            self::CALLS_DATA => $unattendedCalls->getList($dateStart, $period, $uid, $searchWord, $sortField, $sortBy, $page)
        ];
        return json_encode($out);
    }
}
