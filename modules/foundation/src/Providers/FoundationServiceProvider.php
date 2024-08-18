<?php

namespace Dnw\Foundation\Providers;

use App\Models\User;
use Dnw\Foundation\User\UserViewModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class FoundationServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot(): void
    {
        View::composer('*', function ($view) {
            /** @var ?User $user */
            $user = Auth::user();

            $isAuthenticated = Auth::check();
            $userViewModel = new UserViewModel(
                $isAuthenticated,
                $user?->id,
                $user?->name,
            );
            $view->with('user', $userViewModel);
        });
    }
}
