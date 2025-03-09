<?php

namespace Dnw\Game\Domain\Game\Test\Factory;

use Dnw\Game\Domain\Game\ValueObject\Color;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Variant\Collection\VariantPowerCollection;
use Dnw\Game\Domain\Variant\Entity\VariantPower;
use Dnw\Game\Domain\Variant\Shared\VariantKey;
use Dnw\Game\Domain\Variant\Shared\VariantPowerKey;
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
                VariantPowerKey::fromString('AUSTRIA'),
                VariantPowerName::fromString('Austria'),
                Color::fromString('red')
            ),
            new VariantPower(
                VariantPowerKey::fromString('ENGLAND'),
                VariantPowerName::fromString('Great Britain'),
                Color::fromString('pink')
            ),
            new VariantPower(
                VariantPowerKey::fromString('GERMANY'),
                VariantPowerName::fromString('Germany'),
                Color::fromString('brown')
            ),
            new VariantPower(
                VariantPowerKey::fromString('RUSSIA'),
                VariantPowerName::fromString('Russia'),
                Color::fromString('violet')
            ),
            new VariantPower(
                VariantPowerKey::fromString('ITALY'),
                VariantPowerName::fromString('Italy'),
                Color::fromString('green')
            ),
            new VariantPower(
                VariantPowerKey::fromString('FRANCE'),
                VariantPowerName::fromString('France'),
                Color::fromString('blue')
            ),
            new VariantPower(
                VariantPowerKey::fromString('TURKEY'),
                VariantPowerName::fromString('Turkey'),
                Color::fromString('yellow')
            ),
        ]);

        return new Variant(
            VariantKey::fromString('standard'),
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
                VariantPowerKey::fromString('AUSTRIA'),
                VariantPowerName::fromString('China'),
                Color::fromString('red')
            ),
            new VariantPower(
                VariantPowerKey::fromString('ENGLAND'),
                VariantPowerName::fromString('Great Britain'),
                Color::fromString('pink')
            ),
            new VariantPower(
                VariantPowerKey::fromString('JAPAN'),
                VariantPowerName::fromString('Japan'),
                Color::fromString('brown')
            ),
            new VariantPower(
                VariantPowerKey::fromString('RUSSIA'),
                VariantPowerName::fromString('Russia'),
                Color::fromString('violet')
            ),
            new VariantPower(
                VariantPowerKey::fromString('HOLLAND'),
                VariantPowerName::fromString('Holland'),
                Color::fromString('green')
            ),
            new VariantPower(
                VariantPowerKey::fromString('FRANCE'),
                VariantPowerName::fromString('France'),
                Color::fromString('blue')
            ),
            new VariantPower(
                VariantPowerKey::fromString('TURKEY'),
                VariantPowerName::fromString('Turkey'),
                Color::fromString('yellow')
            ),
        ]);

        return new Variant(
            VariantKey::fromString('colonial'),
            VariantName::fromString('Colonial'),
            VariantDescription::fromString('Colonial diplomacy'),
            $variantPowerCollection,
            Count::fromInt(18),
            Count::fromInt(36),
        );
    }
}
