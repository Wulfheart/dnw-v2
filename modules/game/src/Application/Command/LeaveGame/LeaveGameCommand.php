<?php

namespace Dnw\Game\Application\Command\LeaveGame;

use Dnw\Foundation\Bus\Interface\Command;
use Dnw\Foundation\Identity\Id;

/**
 * @implements Command<LeaveGameCommandResult>
 */
class LeaveGameCommand implements Command
{
    public function __construct(
        public Id $gameId,
        public Id $userId,
    ) {}
}
