<?php

namespace ValueObjects\Phases;

use Entity\Phase;
use PhpOption\None;
use PhpOption\Option;
use ValueObjects\Count;

final class PhasesInfo
{
    private bool $hasNewPhase = false;

    public function __construct(
        public Count $count,
        /** @var Option<Phase> $currentPhase */
        public Option $currentPhase,
    ) {
    }

    public static function initialize(): self
    {
        return new self(Count::zero(), None::create());
    }

    public function hasNewPhase(): bool
    {
        return $this->hasNewPhase;
    }
}
