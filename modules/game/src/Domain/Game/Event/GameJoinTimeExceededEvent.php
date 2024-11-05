<?php

namespace Dnw\Game\Domain\Game\Event;

use Dnw\Foundation\Identity\Id;

/**
 * @codeCoverageIgnore
 */
class GameJoinTimeExceededEvent
{
    public function __construct(
        public Id $gameId,
    ) {}
}
