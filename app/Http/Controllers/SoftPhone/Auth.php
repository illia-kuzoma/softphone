<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Models\ReportMissedCall;
use App\Models\User;
use App\Zoho\AuthByPassword;
use Illuminate\Http\Request;

class Auth extends Controller
{
    /**
     * @param  Request  $request
     * @return string
     */
    public function getAuth(Request $request)
    {
        print_r($_POST);
        print_r($_GET);exit;
        $out = [
            'error' => true,
            'message'=>"Please enter correct login and password."
        ];
        return json_encode($out);
    }

    /**
     * @param  Request  $request
     * @return string
     */
    public function postAuth(Request $request)
    {
        print_r($_POST);
        print_r($_GET);
        print_r($request->post());

        exit;
        $email = $request->input('email');
echo $email."!!!";exit;
        $zo = new AuthByPassword();
        $res = $zo->getToken();
        if($res)
        {
            return \Redirect::to('/report/missed/');
        }
        return \Redirect::to('auth');
    }
}
