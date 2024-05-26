<?php

namespace Dnw\Game\Core\Application\Query\GetGame;

use Dnw\Foundation\Identity\Id;

class GetGameQuery
{
    public function __construct(
        public Id $id
    ) {
    }
}
