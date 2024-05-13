<?php

namespace Dnw\Game\Core\Domain\Entity;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Collection\AppliedOrdersCollection;
use Dnw\Game\Core\Domain\Collection\PhasePowerCollection;
use Dnw\Game\Core\Domain\Collection\WinnerCollection;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhaseId;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhasePowerData;
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
        return $this->phasePowerCollection->needsOrdersFromPower($powerId);
    }

    public function ordersMarkedAsReady(PowerId $powerId): bool
    {
        return $this->phasePowerCollection->ordersMarkedAsReadyFromPower($powerId);
    }

    public function markOrderStatus(PowerId $powerId, bool $orderStatus): void
    {
        $this->phasePowerCollection->markOrderStatus($powerId, $orderStatus);
    }

    public function allOrdersMarkedAsReady(): bool
    {
        foreach ($this->phasePowerCollection as $phasePowerData) {
            if ($phasePowerData->ordersNeeded && ! $phasePowerData->markedAsReady) {
                return false;
            }
        }

        return true;
    }

    public function adjudicationTimeExpired(CarbonImmutable $currentTime): bool
    {
        return $this->adjudicationTime->map(
            fn (CarbonImmutable $adjudicationTime) => $currentTime->gt($adjudicationTime)
        )->getOrElse(false);
    }

    public function applyOrders(AppliedOrdersCollection $appliedOrdersCollection): void
    {
        foreach ($appliedOrdersCollection as $item) {
            $phasePowerData = $this->phasePowerCollection
                ->findBy(fn (PhasePowerData $phasePowerData) => $phasePowerData->powerId === $item->powerId);

            $phasePowerData->get()->specifyAppliedOrders(
                $appliedOrdersCollection->findBy(fn ($appliedOrder) => $appliedOrder->powerId === $item->powerId)->get()->orders
            );
        }
    }
}
