<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        echo $_SERVER['HTTP_HOST'];
        // callcentr.wellnessliving.com prod
        //
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
        $s_login = $request->post('email');
        $s_password = $request->post('password');
        try
        {
            $user = new User();
            $user->setUserData($s_login);
            $user->checkUser();

            if(!$user->excluded() && $user->isOldAuth())
            {
                $o_zoho_pass = new AuthByPassword();
                $s_token = $o_zoho_pass->getToken($s_login,$s_password);
            }
            else
            {
                $user->isPasswordCorrect($s_password);
                return \Redirect::to('/report/missed/'.$user->getToken());
            }
        }
        catch(\Exception $e)
        {
            return redirect()->action(
                'SoftPhone\Auth@getAuth', ['message' => $e->getMessage()]
            );
        }

        if(!is_null($s_token))
        {
            try{
                $user->updateUser($s_password, $s_token);
                //$user->updateToken($s_token);
            }
            catch(\Exception $e)
            {
                return redirect()->action(
                    'SoftPhone\Auth@getAuth', ['message' => $e->getMessage()]
                );
            }
            return \Redirect::to('/report/missed/'.$user->getToken());
        }
        return redirect()->action(
            'SoftPhone\Auth@getAuth', ['message' => "Please enter correct login and password."]
        );
    }
}
