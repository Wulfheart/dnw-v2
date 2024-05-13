<?php

namespace Dnw\Game\Core\Application\Command\AdjudicateGame;

use Dnw\Foundation\Identity\Id;

readonly class AdjudicateGameCommand
{
    public function __construct(
        public Id $gameId
    ) {

    }
}
