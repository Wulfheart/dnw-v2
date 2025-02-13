<?php

namespace Dnw\Game\Domain\Game\Event;

use Dnw\Foundation\Identity\Id;

/**
 * @codeCoverageIgnore
 */
class PlayerLeftEvent
{
    public function __construct(
        public Id $gameId,
        public Id $playerId,
    ) {}
}
