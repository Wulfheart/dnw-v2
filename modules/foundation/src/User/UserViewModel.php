<?php

namespace Dnw\Foundation\User;

use Dnw\Foundation\ViewModel\ViewModel;

class UserViewModel extends ViewModel
{
    public function __construct(
        public bool $isAuthenticated,
        public ?string $id,
        public ?string $name
    ) {
    }
}
