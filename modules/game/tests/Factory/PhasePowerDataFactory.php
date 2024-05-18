<?php

namespace Dnw\Game\Tests\Factory;

use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhasePowerData;
use Std\Option;

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
