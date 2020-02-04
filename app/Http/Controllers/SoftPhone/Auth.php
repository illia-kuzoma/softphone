<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Zoho\AuthByPassword;
use Illuminate\Http\Request;

class Auth extends Controller
{
    /**
     * Ответ клиенту.
     * На случай не верного логина или пароля.
     * На случай не верного токена или токена вышедшего по срокам.
     *
     * @param  Request  $request
     * @return string
     */
    public function getAuth(Request $request)
    {
        $out = [
            'error' => true,
            'message'=>$request->get('message')
        ];
        return json_encode($out);
    }

    public function getTest(Request $request)
    {
        print_r($_SERVER);exit();
    }
    /**
     * Авторизация в система. Если успешна - редирект на получение данных по отчету.
     * Если не успешна - редирект на страницу ввода логина и пароля
     *
     * @param  Request  $request
     * @return string
     */
    public function postAuth(Request $request)
    {
        $zo = new AuthByPassword();
        $res = $zo->getToken($request->post('email'));
        if($res)
        {
            return \Redirect::to('/report/missed/');
        }
        return redirect()->action(
            'SoftPhone\Auth@getAuth', ['message' => "Please enter correct login and password."]
        );
        #return redirect()->route('auth', ['message' => "Please enter correct login and password!."]);
    }
}
