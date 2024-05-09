<?php

namespace ValueObjects\GameStartTiming;

final class GameStartTiming
{
    public function __construct(
        public JoinLength $joinLength,
        public bool $startWhenReady,
    ) {

    }
}
