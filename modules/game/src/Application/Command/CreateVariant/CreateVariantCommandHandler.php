<?php

namespace Dnw\Game\Application\Command\CreateVariant;

use Dnw\Game\Domain\Game\ValueObject\Color;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Variant\Collection\VariantPowerCollection;
use Dnw\Game\Domain\Variant\Entity\VariantPower;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Domain\Variant\Shared\VariantKey;
use Dnw\Game\Domain\Variant\Shared\VariantPowerKey;
use Dnw\Game\Domain\Variant\ValueObject\VariantDescription;
use Dnw\Game\Domain\Variant\ValueObject\VariantName;
use Dnw\Game\Domain\Variant\ValueObject\VariantPower\VariantPowerName;
use Dnw\Game\Domain\Variant\Variant;

final readonly class CreateVariantCommandHandler
{
    public function __construct(
        private VariantRepositoryInterface $variantRepository,
    ) {}

    public function handle(CreateVariantCommand $command): CreateVariantCommandResult
    {
        if ($this->variantRepository->keyExists(VariantKey::fromString($command->key))) {
            return CreateVariantCommandResult::err(CreateVariantCommandResult::E_KEY_ALREADY_EXISTS);
        }
        $variantPowerCollection = new VariantPowerCollection();
        foreach ($command->powers as $power) {
            $variantPowerCollection->push(
                new VariantPower(
                    VariantPowerKey::fromString($power->key),
                    VariantPowerName::fromString($power->name),
                    Color::fromString($power->color),
                )
            );
        }
        $variant = new Variant(
            VariantKey::fromString($command->key),
            VariantName::fromString($command->name),
            VariantDescription::fromString($command->description),
            $variantPowerCollection,
            Count::fromInt($command->defaultSupplyCentersToWinCount),
            Count::fromInt($command->totalSupplyCenterCount),
        );

        $this->variantRepository->save($variant);

        return CreateVariantCommandResult::ok();
    }
}
