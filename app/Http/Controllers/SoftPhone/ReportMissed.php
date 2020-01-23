<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Models\MissedCall;
use App\Models\User;
use Illuminate\Http\Request;

class ReportMissed extends Controller
{

    /**
     * @param null $dateStart
     * @param null $dateEnd
     * @return string
     */
    public function getAll($dateStart=null, $dateEnd=null)
    {

//        $missedCalls = MissedCall::all();
//        return view('report.missed.index', compact('missedCalls'));
//        return view('report.missed.index');
        $missedCalls = new MissedCall();
        $user = new User();

        $out = [
            'diagrama' => $missedCalls->getDiagramaList(),
            'calls' => $missedCalls->getCallList(),
            'user' => $user->getData(),
        ];

        return json_encode($out);
    }

    /**
     * @param null $dateStart
     * @param null $dateEnd
     * @param null $uid
     * @param null $searchWord
     * @param null $sortField
     * @param null $sortBy
     * @return string
     */
    public function getCalls($dateStart=null, $dateEnd=null, $uid=null, $searchWord=null, $sortField=null, $sortBy=null)
    {
        $missedCalls = new MissedCall();
        $out = [
            'calls' => $missedCalls->getCallList()
        ];
        return json_encode($out);
    }
}
