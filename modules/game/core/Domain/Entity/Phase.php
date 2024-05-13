<?php

namespace Dnw\Game\Core\Domain\Entity;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Collection\AppliedOrdersCollection;
use Dnw\Game\Core\Domain\Collection\PhasePowerCollection;
use Dnw\Game\Core\Domain\Collection\WinnerCollection;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhaseId;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;
use PhpOption\Option;

class Phase
{
    public function __construct(
        public PhaseId $phaseId,
        public PhaseTypeEnum $phaseType,
        public PhasePowerCollection $phasePowerCollection,
        /** @var Option<CarbonImmutable> $adjudicationTime */
        public Option $adjudicationTime,
        /** @var Option<WinnerCollection> $winnerCollection */
        public Option $winnerCollection,
    ) {

    }

    public function hasWinners(): bool
    {
        return $this->winnerCollection->isDefined();
    }

    public function needsOrders(PowerId $powerId): bool
    {
        return $this->phasePowerCollection->needsOrders($powerId);
    }

    public function markOrderStatus(PowerId $powerId, bool $orderStatus): void
    {
        $this->phasePowerCollection->markOrderStatus($powerId, $orderStatus);
    }

    public function allOrdersMarkedAsReady(): bool
    {

    }

    public function adjudicationTimeExpired(CarbonImmutable $currentTime): bool
    {

    }

    public function applyOrders(AppliedOrdersCollection $appliedOrdersCollection): void
    {

    }
}
