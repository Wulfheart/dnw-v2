<?php

namespace Dnw\Game\Core\Domain\Game\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\Game\Entity\Power;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;
use Dnw\Game\Core\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use Wulfheart\Option\Option;

/**
 * @extends Collection<Power>
 */
class PowerCollection extends Collection
{
    public static function createFromVariantPowerIdCollection(
        VariantPowerIdCollection $variantPowerIdCollection,
    ): self {
        $powers = new self();
        foreach ($variantPowerIdCollection as $item) {
            $powers->push(
                new Power(
                    PowerId::new(),
                    Option::none(),
                    $item,
                    Option::none(),
                    Option::none()
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
            ->filter(fn (Power $power) => $power->playerId->isNone());
    }

    /**
     * @return Collection<Power>
     */
    public function getAssignedPowers(): Collection
    {
        return $this
            ->filter(fn (Power $power) => $power->playerId->isSome());
    }

    public function hasAvailablePowers(): bool
    {
        return $this->filter(
            fn (Power $power) => $power->playerId->isNone()
        )->count() > 0;
    }

    public function hasAllPowersFilled(): bool
    {
        return $this->getUnassignedPowers()->isEmpty();
    }

    public function containsPlayer(PlayerId $playerId): bool
    {
        return $this->findByPlayerId($playerId)->isSome();
    }

    public function doesNotContainPlayer(PlayerId $playerId): bool
    {
        return $this->findByPlayerId($playerId)->isNone();
    }

    public function hasPowerFilled(VariantPowerId $variantPowerId): bool
    {
        return $this->findBy(
            fn (Power $power) => $power->variantPowerId == $variantPowerId
        )->unwrap()->playerId->isSome();
    }

    public function hasNoAssignedPowers(): bool
    {
        foreach ($this as $power) {
            if ($power->playerId->isSome()) {
                return false;
            }
        }

        return true;
    }

    public function getByPlayerId(PlayerId $playerId): Power
    {
        return $this->findByPlayerId($playerId)->unwrap();
    }

    public function getByPowerId(PowerId $powerId): Power
    {
        return $this->findBy(
            fn (Power $power) => $power->powerId == $powerId
        )->unwrap();
    }

    public function getByVariantPowerId(VariantPowerId $variantPowerId): Power
    {
        return $this->findBy(
            fn (Power $power) => $power->variantPowerId == $variantPowerId
        )->unwrap();
    }

    /**
     * @return Option<Power>
     */
    public function findByPlayerId(PlayerId $playerId): Option
    {
        return $this->findBy(
            fn (Power $power) => $power->playerId->mapOr(fn (PlayerId $id): bool => $id == $playerId, false)
        );
    }
}
