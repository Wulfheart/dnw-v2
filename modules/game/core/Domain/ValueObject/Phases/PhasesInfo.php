<?php

namespace Dnw\Game\Core\Domain\ValueObject\Phases;

use Dnw\Game\Core\Domain\Collection\AppliedOrdersCollection;
use Dnw\Game\Core\Domain\Entity\Phase;
use Dnw\Game\Core\Domain\ValueObject\Count;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

class PhasesInfo
{
    private bool $hasNewPhase = false;

    public function __construct(
        public Count $count,
        /** @var Option<Phase> $currentPhase */
        public Option $currentPhase,
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

    public function hasNewPhase(): bool
    {
        return $this->hasNewPhase;
    }

    public function hasBeenStarted(): bool
    {
        return $this->currentPhase->isDefined()
            && $this->currentPhase->get()->adjudicationTime->isDefined();
    }

    public function proceedToNewPhase(Phase $newPhase, AppliedOrdersCollection $appliedOrdersCollection): void
    {
        $this->hasNewPhase = true;
        $this->currentPhase->get()->applyOrders($appliedOrdersCollection);
        $this->previousPhase = $this->currentPhase;
        $this->currentPhase = Some::create($newPhase);
    }
}
