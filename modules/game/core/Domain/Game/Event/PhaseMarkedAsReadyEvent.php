<?php

namespace Dnw\Game\Core\Domain\Game\Event;

use Dnw\Foundation\Identity\Id;

/**
 * @codeCoverageIgnore
 */
class PhaseMarkedAsReadyEvent
{
    public function __construct(
        public Id $gameId,
        public Id $phaseId,
        public Id $powerId,
    ) {}
}
