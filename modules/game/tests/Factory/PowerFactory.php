<?php

namespace Dnw\Game\Tests\Factory;

use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\Entity\Power;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Core\Domain\Game\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

class PowerFactory
{
    public static function unassigned(): Power
    {
        return new Power(
            PowerId::generate(),
            None::create(),
            VariantPowerId::generate(),
            None::create(),
            None::create(),
        );
    }

    public static function assigned(): Power
    {
        return new Power(
            PowerId::generate(),
            Some::create(PlayerId::generate()),
            VariantPowerId::generate(),
            None::create(),
            None::create(),
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
            $id ?? PowerId::generate(),
            //@phpstan-ignore-next-line
            Option::fromValue($playerId),
            $variantPowerId ?? VariantPowerId::generate(),
            //@phpstan-ignore-next-line
            Option::fromValue($currentPhaseData),
            //@phpstan-ignore-next-line
            Option::fromValue($appliedOrders),
        );
    }
}
