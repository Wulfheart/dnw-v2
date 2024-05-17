<?php

namespace Dnw\Game\Core\Domain\Game\Event;

use Dnw\Foundation\Identity\Id;

/**
 * @codeCoverageIgnore
 */
class GameInitializedEvent
{
    public function __construct(
        public Id $gameId,
    ) {
    }
}
