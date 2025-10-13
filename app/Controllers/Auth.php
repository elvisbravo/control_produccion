<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function signin()
    {
        return view('login/signin');
    }

    public function dashboard()
    {
        return view('mobile');
    }
}
