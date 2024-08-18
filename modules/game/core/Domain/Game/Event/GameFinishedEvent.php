<?php

namespace Dnw\Game\Core\Domain\Game\Event;

use Dnw\Foundation\Identity\Id;

class GameFinishedEvent
{
    public function __construct(
        public Id $gameId,
        public int $phaseNumber,
    ) {}
}
