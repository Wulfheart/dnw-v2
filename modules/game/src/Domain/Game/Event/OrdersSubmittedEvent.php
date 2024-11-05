<?php

namespace Dnw\Game\Domain\Game\Event;

use Dnw\Foundation\Identity\Id;

/**
 * @codeCoverageIgnore
 */
class OrdersSubmittedEvent
{
    public function __construct(
        public Id $gameId,
        public Id $phaseId,
        public Id $powerId,
        public bool $markedAsReady,
    ) {}
}
