<?php

namespace Dnw\Game\Domain\Game\Test\Factory;

use Dnw\Game\Domain\Game\ValueObject\Color;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Variant\Collection\VariantPowerCollection;
use Dnw\Game\Domain\Variant\Entity\VariantPower;
use Dnw\Game\Domain\Variant\Shared\VariantId;
use Dnw\Game\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Domain\Variant\ValueObject\VariantDescription;
use Dnw\Game\Domain\Variant\ValueObject\VariantName;
use Dnw\Game\Domain\Variant\ValueObject\VariantPower\VariantPowerName;
use Dnw\Game\Domain\Variant\Variant;

/**
 * @codeCoverageIgnore
 */
class VariantFactory
{
    public static function standard(): Variant
    {
        $variantPowerCollection = new VariantPowerCollection([
            new VariantPower(
                VariantPowerId::fromString('AUSTRIA'),
                VariantPowerName::fromString('Austria'),
                Color::fromString('red')
            ),
            new VariantPower(
                VariantPowerId::fromString('ENGLAND'),
                VariantPowerName::fromString('Great Britain'),
                Color::fromString('pink')
            ),
            new VariantPower(
                VariantPowerId::fromString('GERMANY'),
                VariantPowerName::fromString('Germany'),
                Color::fromString('brown')
            ),
            new VariantPower(
                VariantPowerId::fromString('RUSSIA'),
                VariantPowerName::fromString('Russia'),
                Color::fromString('violet')
            ),
            new VariantPower(
                VariantPowerId::fromString('ITALY'),
                VariantPowerName::fromString('Italy'),
                Color::fromString('green')
            ),
            new VariantPower(
                VariantPowerId::fromString('FRANCE'),
                VariantPowerName::fromString('France'),
                Color::fromString('blue')
            ),
            new VariantPower(
                VariantPowerId::fromString('TURKEY'),
                VariantPowerName::fromString('Turkey'),
                Color::fromString('yellow')
            ),
        ]);

        return new Variant(
            VariantId::fromString('standard'),
            VariantName::fromString('Standard'),
            VariantDescription::fromString('The standard variant of Diplomacy'),
            $variantPowerCollection,
            Count::fromInt(18),
            Count::fromInt(36),
        );
    }

    public static function colonial(): Variant
    {
        $variantPowerCollection = new VariantPowerCollection([
            new VariantPower(
                VariantPowerId::fromString('AUSTRIA'),
                VariantPowerName::fromString('China'),
                Color::fromString('red')
            ),
            new VariantPower(
                VariantPowerId::fromString('ENGLAND'),
                VariantPowerName::fromString('Great Britain'),
                Color::fromString('pink')
            ),
            new VariantPower(
                VariantPowerId::fromString('JAPAN'),
                VariantPowerName::fromString('Japan'),
                Color::fromString('brown')
            ),
            new VariantPower(
                VariantPowerId::fromString('RUSSIA'),
                VariantPowerName::fromString('Russia'),
                Color::fromString('violet')
            ),
            new VariantPower(
                VariantPowerId::fromString('HOLLAND'),
                VariantPowerName::fromString('Holland'),
                Color::fromString('green')
            ),
            new VariantPower(
                VariantPowerId::fromString('FRANCE'),
                VariantPowerName::fromString('France'),
                Color::fromString('blue')
            ),
            new VariantPower(
                VariantPowerId::fromString('TURKEY'),
                VariantPowerName::fromString('Turkey'),
                Color::fromString('yellow')
            ),
        ]);

        return new Variant(
            VariantId::fromString('colonial'),
            VariantName::fromString('Colonial'),
            VariantDescription::fromString('Colonial diplomacy'),
            $variantPowerCollection,
            Count::fromInt(18),
            Count::fromInt(36),
        );
    }
}
