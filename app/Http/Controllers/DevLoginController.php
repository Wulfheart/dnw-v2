<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DevLoginController extends Controller
{
    public function show(): Response
    {
        return response()->view('dev-login');

    }

    public function login(): Response
    {

    }
}
