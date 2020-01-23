<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class Auth extends Controller
{
    /**
     * @return string
     */
    public function auth()
    {
        $user = new User();
        $out = [
            'user' => $user->getData()
        ];
        return json_encode($out);
        #return view('auth.index');
    }
}
