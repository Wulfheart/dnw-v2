<?php

namespace Dnw\Game\Core\Domain\Game\Entity;

use Dnw\Foundation\Exception\DomainException;
use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Core\Domain\Game\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

class Power
{
    public function __construct(
        public PowerId $powerId,
        /** @var Option<PlayerId> $playerId */
        public Option $playerId,
        public VariantPowerId $variantPowerId,
        /** @var Option<PhasePowerData> $currentPhaseData */
        public Option $currentPhaseData,
        /** @var Option<OrderCollection> $appliedOrders */
        public Option $appliedOrders,
    ) {

    }

    public function assign(PlayerId $playerId): void
    {
        if ($this->playerId->isDefined()) {
            throw new DomainException(
                "Power $this->powerId already assigned to player $playerId because {$this->playerId->get()} is already assigned"
            );
        }
        $this->playerId = Some::create($playerId);
    }

    public function unassign(): void
    {
        if ($this->playerId->isEmpty()) {
            throw new DomainException("Power $this->powerId is not assigned to a player");
        }
        $this->playerId = None::create();
    }

    public function markOrderStatus(bool $orderStatus): void
    {
        if ($this->currentPhaseData->isEmpty()) {
            throw new DomainException("Power $this->powerId does not have current phase data");
        }
        $this->currentPhaseData->get()->markedAsReady = $orderStatus;
    }

    public function submitOrders(OrderCollection $orderCollection, bool $markedAsReady): void
    {
        if (! $this->ordersNeeded() || $this->ordersMarkedAsReady()) {
            throw new DomainException("Power $this->powerId is ready for adjudication and cannot submit new orders");
        }
        $this->currentPhaseData->get()->orderCollection = Some::create($orderCollection);
        $this->markOrderStatus($markedAsReady);
    }

    public function ordersNeeded(): bool
    {
        return $this->currentPhaseData->map(
            fn (PhasePowerData $phasePowerData) => $phasePowerData->ordersNeeded
        )->getOrElse(false);
    }

    public function ordersMarkedAsReady(): bool
    {
        return $this->currentPhaseData->map(
            fn (PhasePowerData $phasePowerData) => $phasePowerData->markedAsReady
        )->getOrElse(false);
    }

    public function readyForAdjudication(): bool
    {
        if (! $this->ordersNeeded()) {
            return true;
        }

        return $this->ordersMarkedAsReady();
    }

    public function proceedToNextPhase(PhasePowerData $newPhaseData, OrderCollection $appliedOrders): void
    {
        $this->currentPhaseData = Some::create($newPhaseData);
        $this->appliedOrders = Some::create($appliedOrders);
    }

    public function persistInitialPhase(PhasePowerData $phasePowerData): void
    {
        $this->currentPhaseData = Some::create($phasePowerData);
    }

    public function isDefeated(): bool
    {
        return $this->currentPhaseData->map(
            fn (PhasePowerData $phasePowerData) => $phasePowerData->supplyCenterCount->int() === 0
                && $phasePowerData->unitCount->int() === 0
        )->getOrElse(false);
    }
}
