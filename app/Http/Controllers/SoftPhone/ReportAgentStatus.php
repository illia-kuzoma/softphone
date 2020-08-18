<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Response;
use App\Http\Controllers\Traits\UserAuth;
use App\Models\User;

/**
 * Class ReportAgentStatus use to get agent/agents statuses in time line.
 * @package App\Http\Controllers\SoftPhone
 */
class ReportAgentStatus extends Controller
{
    use UserAuth;
    use Response;

    private const DIAGRAM_DATA = 'diagrama';
    private const STATUS_DATA = 'status';

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
}
