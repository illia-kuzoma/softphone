<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Models\ReportMissedCall;
use App\Models\ReportMissedGraph;
use App\Models\User;
use Illuminate\Http\Request;

class ReportMissed extends Controller
{
    private const DIAGRAM_DATA = 'diagrama';
    private const CALLS_DATA = 'calls';
    private const USER_DATA = 'user';

    /**
     * @param null $dateStart
     * @param null $period
     * @return false|string
     */
    public function getAll($dateStart=null, $period=null): string
    {
        $missedCalls = new ReportMissedCall();
        $user = new User();
        $user->getData($email = 'support@wellnessliving.com');
        if(!$user->checkToken()){
            return redirect()->action(
                'SoftPhone\Auth@getAuth', ['message' => "Please enter to system."]
            );
        }

        $out = [
            self::DIAGRAM_DATA => $missedCalls->getDiagramList($dateStart, $period),
            self::CALLS_DATA => $missedCalls->getList(),
            self::USER_DATA => $user->toArray(),
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
        $missedCalls = new ReportMissedCall();
        $out = [
            self::DIAGRAM_DATA => $missedCalls->getDiagramList($dateStart, $period),
            self::CALLS_DATA => $missedCalls->getList($dateStart, $period, $uid, $searchWord, $sortField, $sortBy, $page)
        ];
        return json_encode($out);
    }
}
