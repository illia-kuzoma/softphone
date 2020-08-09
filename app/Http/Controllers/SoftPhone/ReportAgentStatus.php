<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Response;
use App\Http\Controllers\Traits\UserAuth;

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
        $a_agent_id = [];

        $unattendedCalls = new \App\Models\ReportUnattended($a_agent_id);

        $out = array_merge($this->getResponse($user), [
            self::STATUS_DATA => [],
            self::DIAGRAM_DATA => [],
        ]);

        return json_encode($out);
    }
    //
    public function getAll($token, $dateStart=null, $period=null, $uids=null): string
    {
        return $this->_getAll($token, $dateStart, $period, $uids);
    }
}
