<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Auth extends Controller
{
    public function auth()
    {
        return view('auth.index');
    }
}
