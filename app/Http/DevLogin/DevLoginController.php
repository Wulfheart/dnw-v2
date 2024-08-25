<?php

namespace App\Http\DevLogin;

use App\Models\User;
use App\ViewModel\DevLogin\DevLoginUserInfo;
use App\ViewModel\DevLogin\DevLoginViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DevLoginController
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
            $users,
            route('dev-login.login')
        );

        return response()->view('dev-login', ['viewModel' => $viewModel]);

    }

    public function login(DevLoginRequest $request): RedirectResponse
    {
        Auth::loginUsingId($request->get('userId'));

        return new RedirectResponse(route('game.create'));
    }
}