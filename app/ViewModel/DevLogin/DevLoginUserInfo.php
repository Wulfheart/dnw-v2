<?php

namespace App\ViewModel\DevLogin;

readonly class DevLoginUserInfo
{
    public function __construct(
        public string $name,
        public string $id,
    ) {}
}
