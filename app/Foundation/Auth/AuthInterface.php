<?php

namespace App\Foundation\Auth;

use Dnw\Foundation\Identity\Id;

interface AuthInterface
{
    public function isAuthenticated(): bool;

    public function getUserId(): Id;
}
