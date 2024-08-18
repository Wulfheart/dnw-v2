<?php

namespace App\Http\Controllers\DevLogin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\ViewModel\DevLogin\DevLoginUserInfo;
use App\ViewModel\DevLogin\DevLoginViewModel;
use Illuminate\Http\Response;

class DevLoginController extends Controller
{
    public function show(): Response
    {
        $users = User::orderBy('name')->get()->map(function (User $user) {
            return new DevLoginUserInfo(
                $user->name,
                $user->id,
            );
        })->toArray();

        $viewModel = new DevLoginViewModel(
            $users
        );

        return response()->view('dev-login', ['viewModel' => $viewModel]);

    }

    public function login(DevLoginRequest $request): Response
    {
        return response();
    }
}
