<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Models\ReportMissedCall;
use App\Models\User;
use Illuminate\Http\Request;

class Auth extends Controller
{
    /**
     * @param  Request  $request
     * @return string
     */
    public function getAuth(Request $request)
    {
        $user = new User();
        $out = [
            'user' => $user->getData()
        ];
        return json_encode($out);
        #return view('auth.index');
    }

    /**
     * @param  Request  $request
     * @return string
     */
    public function postAuth()
    {
        $user = new User();
        $missedCalls = new ReportMissedCall();
        $out = [
            'diagrama' => $missedCalls->getDiagramaList(),
            'calls' => $missedCalls->getList(),
            'user' => $user->getData(),
        ];
        return json_encode($out);
        #return view('auth.index');
    }
}
