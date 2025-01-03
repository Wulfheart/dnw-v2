<?php

namespace Dnw\Game\Application\Command\InitialGameAdjudication;

use Dnw\Foundation\Bus\Interface\Command;
use Dnw\Foundation\Identity\Id;

/**
 * @implements Command<InitialGameAdjudicationCommandResult>
 */
class InitialGameAdjudicationCommand implements Command
{
    public function __construct(
        public Id $gameId
    ) {}
}
