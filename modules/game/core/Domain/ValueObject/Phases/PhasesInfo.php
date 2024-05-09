<?php

namespace Dnw\Game\Core\Domain\ValueObject\Phases;

use Dnw\Game\Core\Domain\Entity\Phase;
use Dnw\Game\Core\Domain\ValueObject\Count;
use PhpOption\None;
use PhpOption\Option;

class PhasesInfo
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
