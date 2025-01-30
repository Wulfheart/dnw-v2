<?php

namespace App\Foundation\Id;

use Dnw\Foundation\Identity\Id;

interface IdGeneratorInterface
{
    public function generate(): Id;
}
