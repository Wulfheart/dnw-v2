<?php

namespace App\ViewModel\DevLogin;

readonly class DevLoginViewModel
{
    public function __construct(
        /** @var array<DevLoginUserInfo> $users */
        public array $users,
    ) {}
}
