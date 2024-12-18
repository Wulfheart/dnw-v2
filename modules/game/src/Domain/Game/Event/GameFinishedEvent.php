<?php

namespace Dnw\Game\Domain\Game\Event;

use Dnw\Foundation\Identity\Id;

/**
 * @codeCoverageIgnore
 */
class GameFinishedEvent
{
    public function __construct(
        public Id $gameId,
        public int $phaseNumber,
    ) {}
}
