<?php

namespace App\ViewModel\User;

use Std\Option;

class UserInfoViewModel
{
    public function __construct(
        public bool $isAuthenticated,
        /** @var Option<string> $name */
        public Option $name,
        /** @var Option<string> $id */
        public Option $id,
    ) {}
}
