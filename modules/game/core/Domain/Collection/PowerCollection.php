<?php

namespace Dnw\Game\Core\Domain\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\Entity\Power;
use Dnw\Game\Core\Domain\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantPower\VariantPowerId;
use DomainException;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

/**
 * @extends Collection<Power>
 */
class PowerCollection extends Collection
{
    public static function createFromVariantPowerCollection(
        VariantPowerCollection $variantPowerCollection,
    ): self {
        $powers = new self();
        foreach ($variantPowerCollection as $item) {
            $powers->push(
                new Power(
                    PowerId::generate(),
                    None::create(),
                    $item->id,
                    false,
                )
            );
        }

        return $powers;
    }

    /**
     * @return Collection<Power>
     */
    public function getUnassignedPowers(): Collection
    {
        return $this
            ->filter(fn (Power $power) => $power->playerId->isEmpty());
    }

    public function assign(PlayerId $playerId, VariantPowerId $variantPowerId): void
    {
        $power = $this->findBy(
            fn (Power $power) => $variantPowerId === $power->variantPowerId
        )->get();

        if ($power->playerId->isDefined()) {
            throw new DomainException("Power $power->powerId already assigned to a player");
        }

        $power->playerId = Some::create($playerId);
    }

    public function hasAvailablePowers(): bool
    {
        return $this->filter(
            fn (Power $power) => $power->playerId->isEmpty()
        )->count() > 0;
    }

    public function unassign(PlayerId $playerId): void
    {
        $power = $this->findBy(
            fn (Power $power) => $playerId === $power->playerId->get()
        )->get();

        if ($power->playerId->isEmpty()) {
            throw new DomainException('Power not assigned');
        }

        $power->playerId = None::create();
    }

    public function containsPlayer(PlayerId $playerId): bool
    {
        return $this->findByPlayerId($playerId)->isDefined();
    }

    public function doesNotContainPlayer(PlayerId $playerId): bool
    {
        return $this->findByPlayerId($playerId)->isEmpty();
    }

    public function hasPowerFilled(VariantPowerId $variantPowerId): bool
    {
        return $this->findBy(
            fn (Power $power) => $power->variantPowerId === $variantPowerId
        )->isDefined();
    }

    public function hasNoAssignedPowers(): bool
    {
        foreach ($this as $power) {
            if ($power->playerId->isDefined()) {
                return false;
            }
        }

        return true;
    }

    public function getPowerIdByPlayerId(PlayerId $playerId): PowerId
    {
        return $this->findByPlayerId($playerId)->get()->powerId;
    }

    public function getByPowerId(PowerId $powerId): Power
    {
        return $this->findBy(
            fn (Power $power) => $power->powerId === $powerId
        )->get();
    }

    public function getByVariantPowerId(VariantPowerId $variantPowerId): Power
    {
        return $this->findBy(
            fn (Power $power) => $power->variantPowerId === $variantPowerId
        )->get();
    }

    /**
     * @return Option<Power>
     */
    private function findByPlayerId(PlayerId $playerId): Option
    {
        return $this->findBy(
            fn (Power $power) => $power->playerId->map(fn (PlayerId $id): bool => $id === $playerId)->getOrElse(false)
        );
    }
}
