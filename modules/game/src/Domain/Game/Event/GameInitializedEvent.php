<?php

namespace Dnw\Game\Domain\Game\Event;

use Dnw\Foundation\Identity\Id;

/**
 * @codeCoverageIgnore
 */
class GameInitializedEvent
{
    public function __construct(
        public Id $gameId,
    ) {}
}
