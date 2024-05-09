<?php

namespace Dnw\Game\Core\Domain\ValueObject\GameStartTiming;

class GameStartTiming
{
    public function __construct(
        public JoinLength $joinLength,
        public bool $startWhenReady,
    ) {

    }
}
