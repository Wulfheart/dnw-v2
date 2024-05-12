<?php

namespace Dnw\Game\Core\Application\Command\LeaveGame;

use Dnw\Foundation\Identity\Id;

class LeaveGameCommand
{
    public function __construct(
        public Id $gameId,
        public Id $userId,
    ) {
    }
}
