<?php

namespace Dnw\Game\Domain\Game\Event;

use Dnw\Foundation\Identity\Id;

/**
 * @codeCoverageIgnore
 */
class GameReadyForAdjudicationEvent
{
    public function __construct(
        public Id $gameId,
    ) {}
}
