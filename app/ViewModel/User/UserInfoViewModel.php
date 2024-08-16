<?php

namespace App\ViewModel\User;

use Dnw\Foundation\ViewModel\ViewModel;
use Std\Option;

class UserInfoViewModel extends ViewModel {
    public function __construct(
        public bool $isAuthenticated,
        /** @var Option<string> $name */
        public Option $name,
        /** @var Option<string> $id */
        public Option $id,
    ) {

    }
}
