<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\Phases;

use Dnw\Game\Core\Domain\Game\Collection\AppliedOrdersCollection;
use Dnw\Game\Core\Domain\Game\Entity\Phase;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

class PhasesInfo
{
    public function __construct(
        public Count $count,
        /** @var Option<Phase> $currentPhase */
        public Option $currentPhase,
        /** @var Option<Phase> $previousPhase */
        public Option $previousPhase,
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

    public function proceedToNewPhase(Phase $newPhase, AppliedOrdersCollection $appliedOrdersCollection): void
    {
        $this->currentPhase->get()->applyOrders($appliedOrdersCollection);
        $this->previousPhase = $this->currentPhase;
        $this->currentPhase = Some::create($newPhase);
    }

    public function setInitialPhase(Phase $phase): void
    {
        $this->currentPhase = Some::create($phase);
        $this->count = Count::fromInt(1);
    }
}
