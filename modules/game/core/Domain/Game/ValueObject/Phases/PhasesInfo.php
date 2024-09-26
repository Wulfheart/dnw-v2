<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\Phases;

use Dnw\Game\Core\Domain\Game\Entity\Phase;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;
use Wulfeart\Option\Option;

class PhasesInfo
{
    public function __construct(
        public Count $count,
        /** @var Option<Phase> $currentPhase */
        public Option $currentPhase,
        /** @var Option<PhaseId> $lastPhaseId */
        public Option $lastPhaseId,
    ) {}

    public static function initialize(): self
    {
        return new self(Count::zero(), Option::none(), Option::none());
    }

    public function initialPhaseExists(): bool
    {
        return $this->count->int() >= 1;
    }

    public function hasBeenStarted(): bool
    {
        return $this->currentPhase->isSome()
            && $this->currentPhase->unwrap()->adjudicationTime->isSome();
    }

    public function proceedToNewPhase(Phase $newPhase): void
    {
        $this->count = Count::fromInt($this->count->int() + 1);
        $this->lastPhaseId = Option::some($this->currentPhase->unwrap()->phaseId);
        $this->currentPhase = Option::some($newPhase);
    }

    public function setInitialPhase(Phase $phase): void
    {
        $this->currentPhase = Option::some($phase);
        $this->count = Count::fromInt(1);
    }
}
