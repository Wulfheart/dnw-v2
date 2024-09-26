<?php

namespace App\Providers;

use App\Models\User;
use App\ViewModel\User\UserInfoViewModel;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use Wulfheart\Option\Option;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        Facades\View::composer('*', function (View $view) {
            $isGuest = Auth::guest();
            if ($isGuest) {
                $user = new UserInfoViewModel(
                    isAuthenticated: false,
                    name: Option::none(),
                    id: Option::none(),
                );
            } else {
                /** @var User $authUser */
                $authUser = Auth::user();
                $user = new UserInfoViewModel(
                    isAuthenticated: true,
                    name: Option::some($authUser->name),
                    id: Option::some($authUser->id),
                );
            }
            $view->with('userInfo', $user);
        });

    }
}
