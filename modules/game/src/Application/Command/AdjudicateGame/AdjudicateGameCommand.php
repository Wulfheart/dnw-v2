<?php

namespace Dnw\Game\Application\Command\AdjudicateGame;

use Dnw\Foundation\Identity\Id;

readonly class AdjudicateGameCommand
{
    public function __construct(
        public Id $gameId
    ) {}
}
