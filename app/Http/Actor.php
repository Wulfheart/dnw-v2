<?php

namespace App\Http;

use Dnw\Foundation\Identity\Id;

final readonly class Actor
{
    public function __construct(
        public Id $userId,
    ) {}
}
