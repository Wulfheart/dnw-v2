<?php

namespace Dnw\Game\Core\Domain\Game\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\Game\Entity\Power;
use Dnw\Game\Core\Domain\Game\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use PhpOption\None;
use PhpOption\Option;

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
                    None::create(),
                    $item,
                    None::create(),
                    None::create()
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

    /**
     * @return Collection<Power>
     */
    public function getAssignedPowers(): Collection
    {
        return $this
            ->filter(fn (Power $power) => $power->playerId->isDefined());
    }

    public function hasAvailablePowers(): bool
    {
        return $this->filter(
            fn (Power $power) => $power->playerId->isEmpty()
        )->count() > 0;
    }

    public function hasAllPowersFilled(): bool
    {
        return $this->getUnassignedPowers()->isEmpty();
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
            fn (Power $power) => $power->variantPowerId == $variantPowerId
        )->get()->playerId->isDefined();
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

    public function getByPlayerId(PlayerId $playerId): Power
    {
        return $this->findByPlayerId($playerId)->get();
    }

    public function getByPowerId(PowerId $powerId): Power
    {
        return $this->findBy(
            fn (Power $power) => $power->powerId == $powerId
        )->get();
    }

    public function getByVariantPowerId(VariantPowerId $variantPowerId): Power
    {
        return $this->findBy(
            fn (Power $power) => $power->variantPowerId == $variantPowerId
        )->get();
    }

    /**
     * @return Option<Power>
     */
    public function findByPlayerId(PlayerId $playerId): Option
    {
        return $this->findBy(
            fn (Power $power) => $power->playerId->map(fn (PlayerId $id): bool => $id == $playerId)->getOrElse(false)
        );
    }
}
