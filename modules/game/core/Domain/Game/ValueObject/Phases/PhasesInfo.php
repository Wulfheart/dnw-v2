<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\Phases;

use Dnw\Game\Core\Domain\Game\Entity\Phase;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

class PhasesInfo
{
    public function __construct(
        public Count $count,
        /** @var Option<Phase> $currentPhase */
        public Option $currentPhase,
        /** @var Option<PhaseId> $lastPhaseId */
        public Option $lastPhaseId,
    ) {
    }

    public static function initialize(): self
    {
        return new self(Count::zero(), None::create(), None::create());
    }

    public function initialPhaseExists(): bool
    {
        return $this->count->int() >= 1;
    }

    public function hasBeenStarted(): bool
    {
        return $this->currentPhase->isDefined()
            && $this->currentPhase->get()->adjudicationTime->isDefined();
    }

    public function proceedToNewPhase(Phase $newPhase): void
    {
        $this->count = Count::fromInt($this->count->int() + 1);
        $this->lastPhaseId = Some::create($this->currentPhase->get()->phaseId);
        $this->currentPhase = Some::create($newPhase);
    }

    public function setInitialPhase(Phase $phase): void
    {
        $this->currentPhase = Some::create($phase);
        $this->count = Count::fromInt(1);
    }
}
