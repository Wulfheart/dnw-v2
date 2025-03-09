<?php

namespace Dnw\Game\Domain\Game\Test\Factory;

use Dnw\Foundation\Identity\Id;
use Dnw\Game\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Domain\Game\Entity\Power;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Domain\Game\ValueObject\Power\PowerId;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Domain\Variant\Shared\VariantPowerId;
use Wulfheart\Option\Option;

/**
 * @codeCoverageIgnore
 */
class PowerFactory
{
    public static function unassigned(): Power
    {
        return new Power(
            PowerId::new(),
            Option::none(),
            VariantPowerId::fromString(Id::generate()),
            Option::none(),
            Option::none(),
        );
    }

    public static function assigned(): Power
    {
        return new Power(
            PowerId::new(),
            Option::some(PlayerId::new()),
            VariantPowerId::fromString(Id::generate()),
            Option::none(),
            Option::none(),
        );
    }

    public static function build(
        ?PowerId $id = null,
        ?PlayerId $playerId = null,
        ?VariantPowerId $variantPowerId = null,
        ?PhasePowerData $currentPhaseData = null,
        ?OrderCollection $appliedOrders = null,
    ): Power {

        return new Power(
            $id ?? PowerId::new(),
            Option::fromNullable($playerId),
            $variantPowerId ?? VariantPowerId::fromString(Id::generate()),
            Option::fromNullable($currentPhaseData),
            Option::fromNullable($appliedOrders),
        );
    }
}
