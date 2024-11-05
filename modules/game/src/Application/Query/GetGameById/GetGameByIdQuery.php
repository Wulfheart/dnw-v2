<?php

namespace Dnw\Game\Application\Query\GetGameById;

use Dnw\Foundation\Identity\Id;

class GetGameByIdQuery
{
    public function __construct(
        public Id $id
    ) {}
}
