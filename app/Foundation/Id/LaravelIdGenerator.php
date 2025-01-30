<?php

namespace App\Foundation\Id;

use Dnw\Foundation\Identity\Id;

final class LaravelIdGenerator implements IdGeneratorInterface
{
    public function generate(): Id
    {
        return Id::generate();
    }
}
