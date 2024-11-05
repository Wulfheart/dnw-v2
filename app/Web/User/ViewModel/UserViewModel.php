<?php

namespace App\Web\User\ViewModel;

/**
 * @codeCoverageIgnore
 */
class UserViewModel
{
    public function __construct(
        public bool $isAuthenticated,
        public ?string $id,
        public ?string $name
    ) {}
}
