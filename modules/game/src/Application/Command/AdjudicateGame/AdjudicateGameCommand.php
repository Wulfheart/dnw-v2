<?php

namespace Dnw\Game\Application\Command\AdjudicateGame;

use Dnw\Foundation\Bus\Interface\Command;
use Dnw\Foundation\Identity\Id;

/**
 * @implements Command<AdjudicateGameCommandResult>
 */
readonly class AdjudicateGameCommand implements Command
{
    public function __construct(
        public Id $gameId
    ) {}
}
