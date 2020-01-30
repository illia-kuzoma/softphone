<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Models\ReportMissedCall;
use App\Models\ReportMissedGraph;
use App\Models\User;
use Illuminate\Http\Request;

class ReportMissed extends Controller
{

    /**
     * @param null $dateStart
     * @param null $period
     * @return false|string
     */
    public function getAll($dateStart=null, $period=null): string
    {
        $missedCalls = new ReportMissedCall();
        $user = new User();
        $out = [
            'diagrama' => $missedCalls->getDiagramList($dateStart, $period),
            'calls' => $missedCalls->getList(),
            'user' => $user->getData($email = 'support@wellnessliving.com'),
        ];

        return json_encode($out);
    }

    /**
     * @param null $dateStart
     * @param null $period
     * @param null $uid
     * @param null $searchWord
     * @param null $sortField
     * @param null $sortBy
     * @param int $page
     *
     * @return string
     */
    public function getCalls($dateStart=null, $period=null, $uid=null, $searchWord=null, $sortField=null, $sortBy='DESC', $page = 1): string
    {
        $missedCalls = new ReportMissedCall();
        #echo $dateStart . " " . $period;exit;

        $out = [
            'diagrama' => $missedCalls->getDiagramList($dateStart, $period),
            'calls' => $missedCalls->getList($dateStart, $period, $uid, $searchWord, $sortField, $sortBy, $page)
        ];
        return json_encode($out);
    }
}
