<?php

namespace Dnw\Game\Tests\Mother;

use Dnw\Game\Core\Domain\Game\Collection\VariantPowerCollection;
use Dnw\Game\Core\Domain\Game\Entity\Variant;
use Dnw\Game\Core\Domain\Game\Entity\VariantPower;
use Dnw\Game\Core\Domain\Game\ValueObject\Color;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantApiName;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantId;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantName;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantPower\VariantPowerApiName;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantPower\VariantPowerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantPower\VariantPowerName;

class VariantMother
{
    public static function standard(): Variant
    {
        $variantPowerCollection = new VariantPowerCollection([
            new VariantPower(
                VariantPowerId::generate(),
                VariantPowerName::fromString('Austria'),
                VariantPowerApiName::fromString('austria'),
                Color::fromString('red')
            ),
            new VariantPower(
                VariantPowerId::generate(),
                VariantPowerName::fromString('Great Britain'),
                VariantPowerApiName::fromString('GB'),
                Color::fromString('pink')
            ),
            new VariantPower(
                VariantPowerId::generate(),
                VariantPowerName::fromString('Germany'),
                VariantPowerApiName::fromString('germany'),
                Color::fromString('brown')
            ),
            new VariantPower(
                VariantPowerId::generate(),
                VariantPowerName::fromString('Russia'),
                VariantPowerApiName::fromString('russia'),
                Color::fromString('violet')
            ),
            new VariantPower(
                VariantPowerId::generate(),
                VariantPowerName::fromString('Italy'),
                VariantPowerApiName::fromString('italy'),
                Color::fromString('green')
            ),
            new VariantPower(
                VariantPowerId::generate(),
                VariantPowerName::fromString('France'),
                VariantPowerApiName::fromString('france'),
                Color::fromString('blue')
            ),
            new VariantPower(
                VariantPowerId::generate(),
                VariantPowerName::fromString('Turkey'),
                VariantPowerApiName::fromString('turkey'),
                Color::fromString('yellow')
            ),
        ]);

        return new Variant(
            VariantId::generate(),
            VariantName::fromString('Standard'),
            VariantApiName::fromString('standard'),
            $variantPowerCollection,
            Count::fromInt(18),
            Count::fromInt(36),
        );
    }
}
