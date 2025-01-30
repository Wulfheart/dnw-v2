<?php

namespace App\Foundation\Auth;

use Dnw\Foundation\Identity\Id;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

final class LaravelAuthProvider implements AuthInterface
{
    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    public function getUserId(): Id
    {
        if ($this->isAuthenticated()) {
            /** @var string $id */
            $id = Auth::id();

            return Id::fromString($id);
        } else {
            throw new AuthenticationException();
        }
    }
}
