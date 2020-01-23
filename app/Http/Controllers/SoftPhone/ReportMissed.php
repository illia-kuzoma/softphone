<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Models\MissedCall;
use Illuminate\Http\Request;

class ReportMissed extends Controller
{

    /**
     * @param null $dateStart
     * @param null $dateEnd
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAll($dateStart=null, $dateEnd=null)
    {
//        $missedCalls = MissedCall::all();
//        return view('report.missed.index', compact('missedCalls'));
        return view('report.missed.index');
    }

    /**
     * @param null $dateStart
     * @param null $dateEnd
     * @param null $uid
     * @param null $searchWord
     * @param null $sortField
     * @param null $sortBy
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCalls($dateStart=null, $dateEnd=null, $uid=null, $searchWord=null, $sortField=null, $sortBy=null)
    {
        return view('report.missed.calls');
    }
}
