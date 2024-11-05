<?php

namespace Dnw\Game\Domain\Game\Test\Factory;

use Dnw\Game\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhasePowerData;
use Wulfheart\Option\Option;

/**
 * @codeCoverageIgnore
 */
class PhasePowerDataFactory
{
    public static function build(
        ?bool $ordersNeeded = null,
        ?bool $markedAsReady = null,
        ?bool $isWinner = null,
        ?Count $supplyCenterCount = null,
        ?Count $unitCount = null,
        ?OrderCollection $orderCollection = null,
    ): PhasePowerData {
        return new PhasePowerData(
            $ordersNeeded ?? false,
            $markedAsReady ?? false,
            $isWinner ?? false,
            $supplyCenterCount ?? new Count(0),
            $unitCount ?? new Count(0),
            Option::fromNullable($orderCollection),
        );
    }
}
