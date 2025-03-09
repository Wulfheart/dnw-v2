<?php

namespace Dnw\Game\Domain\Game\Test\Factory;

use Dnw\Game\Domain\Game\ValueObject\Color;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Variant\Collection\VariantPowerCollection;
use Dnw\Game\Domain\Variant\Entity\VariantPower;
use Dnw\Game\Domain\Variant\Shared\VariantId;
use Dnw\Game\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Domain\Variant\ValueObject\VariantApiName;
use Dnw\Game\Domain\Variant\ValueObject\VariantDescription;
use Dnw\Game\Domain\Variant\ValueObject\VariantName;
use Dnw\Game\Domain\Variant\ValueObject\VariantPower\VariantPowerApiName;
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
                VariantPowerId::new('austria'),
                VariantPowerName::fromString('Austria'),
                VariantPowerApiName::fromString('AUSTRIA'),
                Color::fromString('red')
            ),
            new VariantPower(
                VariantPowerId::new('greatbritain'),
                VariantPowerName::fromString('Great Britain'),
                VariantPowerApiName::fromString('ENGLAND'),
                Color::fromString('pink')
            ),
            new VariantPower(
                VariantPowerId::new('germany'),
                VariantPowerName::fromString('Germany'),
                VariantPowerApiName::fromString('GERMANY'),
                Color::fromString('brown')
            ),
            new VariantPower(
                VariantPowerId::new('russia'),
                VariantPowerName::fromString('Russia'),
                VariantPowerApiName::fromString('RUSSIA'),
                Color::fromString('violet')
            ),
            new VariantPower(
                VariantPowerId::new('italy'),
                VariantPowerName::fromString('Italy'),
                VariantPowerApiName::fromString('ITALY'),
                Color::fromString('green')
            ),
            new VariantPower(
                VariantPowerId::new('france'),
                VariantPowerName::fromString('France'),
                VariantPowerApiName::fromString('FRANCE'),
                Color::fromString('blue')
            ),
            new VariantPower(
                VariantPowerId::new('turkey'),
                VariantPowerName::fromString('Turkey'),
                VariantPowerApiName::fromString('TURKEY'),
                Color::fromString('yellow')
            ),
        ]);

        return new Variant(
            VariantId::fromString('standard'),
            VariantName::fromString('Standard'),
            VariantApiName::fromString('standard'),
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
                VariantPowerId::new('austria'),
                VariantPowerName::fromString('China'),
                VariantPowerApiName::fromString('AUSTRIA'),
                Color::fromString('red')
            ),
            new VariantPower(
                VariantPowerId::new('greatbritain'),
                VariantPowerName::fromString('Great Britain'),
                VariantPowerApiName::fromString('ENGLAND'),
                Color::fromString('pink')
            ),
            new VariantPower(
                VariantPowerId::new('japan'),
                VariantPowerName::fromString('Japan'),
                VariantPowerApiName::fromString('JAPAN'),
                Color::fromString('brown')
            ),
            new VariantPower(
                VariantPowerId::new('russia'),
                VariantPowerName::fromString('Russia'),
                VariantPowerApiName::fromString('RUSSIA'),
                Color::fromString('violet')
            ),
            new VariantPower(
                VariantPowerId::new('holland'),
                VariantPowerName::fromString('Holland'),
                VariantPowerApiName::fromString('ITALY'),
                Color::fromString('green')
            ),
            new VariantPower(
                VariantPowerId::new('france'),
                VariantPowerName::fromString('France'),
                VariantPowerApiName::fromString('FRANCE'),
                Color::fromString('blue')
            ),
            new VariantPower(
                VariantPowerId::new('turkey'),
                VariantPowerName::fromString('Turkey'),
                VariantPowerApiName::fromString('TURKEY'),
                Color::fromString('yellow')
            ),
        ]);

        return new Variant(
            VariantId::fromString('colonial'),
            VariantName::fromString('Colonial'),
            VariantApiName::fromString('colonial'),
            VariantDescription::fromString('Colonial diplomacy'),
            $variantPowerCollection,
            Count::fromInt(18),
            Count::fromInt(36),
        );
    }
}
