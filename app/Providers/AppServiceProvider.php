<?php

namespace App\Providers;

use App\Foundation\Auth\AuthInterface;
use App\Foundation\Auth\LaravelAuthProvider;
use App\Foundation\Id\IdGeneratorInterface;
use App\Foundation\Id\LaravelIdGenerator;
use App\ViewModel\User\UserInfoViewModel;
use Auth;
use Dnw\User\Infrastructure\UserModel;
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
                /** @var UserModel $authUser */
                $authUser = Auth::user();
                /** @var string $authUserId */
                $authUserId = $authUser->id;
                $user = new UserInfoViewModel(
                    isAuthenticated: true,
                    name: Option::some($authUser->name),
                    id: Option::some($authUserId),
                );
            }
            $view->with('userInfo', $user);
        });

        $this->app->bind(
            AuthInterface::class,
            LaravelAuthProvider::class,
        );
        $this->app->bind(
            IdGeneratorInterface::class,
            LaravelIdGenerator::class
        );

    }
}
