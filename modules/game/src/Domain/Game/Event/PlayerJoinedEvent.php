<?php

namespace Dnw\Game\Domain\Game\Event;

use Dnw\Foundation\Identity\Id;

/**
 * @codeCoverageIgnore
 */
class PlayerJoinedEvent
{
    public function __construct(
        public Id $gameId,
        public Id $powerId,
    ) {}
}
