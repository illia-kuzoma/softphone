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
//        return view('report.missed.index');
        $out = [
            'diagrama' => [
                'user_id' => '1',
                'first_name' => 'Ivan',
                'last_name' => 'Petrov',
                'calls_count' => 0,
            ],
            'calls' => [
                'data' => [
                    'user_id' => '1',
                    'photo_url' => 'https://www.bmw-motorsport.com/content/dam/bmw/marketBMWSPORTS/bmw-motorsport_com/assets/bmw-m-motorsport/race-cars/bmw-m2-cs-racing/bmw-m2-cs-racing-ascari-hotspot.jpg',
                    'first_name' => 'Ivan',
                    'last_name' => 'Petrov',
                    'business_name' => 'Bavaria Motors LLC',
                    'contact' => 'UFO',
                    'priority' => 'low',
                    'phone' => '+380508008080',
                    'time_create' => '1579996800',
                ],
                'page_count' => 1,
                'page' => 1
            ],
            'user' => [
                'user_id' => 1,
                'photo_url' => 'https://www.bmw-motorsport.com/content/dam/bmw/marketBMWSPORTS/bmw-motorsport_com/assets/bmw-m-motorsport/race-cars/bmw-m2-cs-racing/bmw-m2-cs-racing-ascari-hotspot.jpg',
                'first_name' => 'Ivan',
                'last_name' => 'Petrov',
                'role' => 'user'
            ],
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCalls($dateStart=null, $dateEnd=null, $uid=null, $searchWord=null, $sortField=null, $sortBy=null)
    {
        return view('report.missed.calls');
    }
}
