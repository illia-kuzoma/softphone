<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Models\ReportMissedCall;
use App\Models\User;
use Illuminate\Http\Request;

class ReportMissed extends Controller
{

    /**
     * @param null $dateStart
     * @param null $period
     * @return string
     */
    public function getAll($dateStart=null, $period=null)
    {

//        $missedCalls = MissedCall::all();
//        return view('report.missed.index', compact('missedCalls'));
//        return view('report.missed.index');
        $missedCalls = new ReportMissedCall();
        $user = new User();

        $out = [
            'diagrama' => $missedCalls->getDiagramaList(),
            'calls' => $missedCalls->getList(),
            'user' => $user->getData(),
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
     * @return string
     */
    public function getCalls($dateStart=null, $period=null, $uid=null, $searchWord=null, $sortField=null, $sortBy=null)
    {
        #echo $dateStart . " " . $period;exit;
        $missedCalls = new ReportMissedCall();
        $out = [
            'calls' => $missedCalls->getList()
        ];
        return json_encode($out);
    }
}
