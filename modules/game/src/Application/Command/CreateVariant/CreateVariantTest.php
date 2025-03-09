<?php

namespace Dnw\Game\Application\Command\CreateVariant;

use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsQuery;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\ModuleTestCase;
use Wulfheart\Option\ResultAsserter;

#[CoversClass(CreateVariantCommandHandler::class)]
final class CreateVariantTest extends ModuleTestCase
{
    public function test_adds_variant(): void
    {
        $command = new CreateVariantCommand(
            '::KEY::',
            '::NAME::',
            '::DESCRIPTION::',
            18,
            36,
            ArrayCollection::fromArray([
                new VariantPowerInfo('::POWER_KEY_1::', '::POWER_NAME_1::', '::POWER_DESCRIPTION_1::'),
                new VariantPowerInfo('::POWER_KEY_2::', '::POWER_NAME_2::', '::POWER_DESCRIPTION_2::'),
            ])
        );

        $result = $this->bus->handle($command);
        ResultAsserter::assertOk($result);

        $variants = $this->bus->handle(new GetAllVariantsQuery());
        $this->assertCount(1, $variants->variants);
        $this->assertEquals('::KEY::', $variants->variants[0]->key);

    }

    public function test_fails_if_variant_key_already_exists(): void
    {
        $command = new CreateVariantCommand(
            '::KEY::',
            '::NAME::',
            '::DESCRIPTION::',
            18,
            36,
            ArrayCollection::fromArray([
                new VariantPowerInfo('::POWER_KEY_1::', '::POWER_NAME_1::', '::POWER_DESCRIPTION_1::'),
                new VariantPowerInfo('::POWER_KEY_2::', '::POWER_NAME_2::', '::POWER_DESCRIPTION_2::'),
            ])
        );

        $result = $this->bus->handle($command);
        ResultAsserter::assertOk($result);

        $result = $this->bus->handle($command);
        ResultAsserter::assertErrIs($result, CreateVariantCommandResult::E_KEY_ALREADY_EXISTS);
    }
}
