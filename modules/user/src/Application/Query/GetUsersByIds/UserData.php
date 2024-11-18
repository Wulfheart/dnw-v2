<?php

namespace Dnw\User\Application\Query\GetUsersByIds;

use Dnw\Foundation\Identity\Id;

final class UserData
{
    public function __construct(
        public Id $id,
        public string $name
    ) {}
}
