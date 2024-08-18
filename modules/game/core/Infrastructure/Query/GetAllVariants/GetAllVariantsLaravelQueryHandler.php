<?php

namespace Dnw\Game\Core\Infrastructure\Query\GetAllVariants;

use Dnw\Foundation\Identity\Id;
use Dnw\Game\Core\Application\Query\GetAllVariants\GetAllVariantsQuery;
use Dnw\Game\Core\Application\Query\GetAllVariants\GetAllVariantsQueryHandlerInterface;
use Dnw\Game\Core\Application\Query\GetAllVariants\VariantDto;
use Dnw\Game\Core\Application\Query\GetAllVariants\VariantPowerDto;
use Dnw\Game\Core\Infrastructure\Model\Variant\VariantModel;

class GetAllVariantsLaravelQueryHandler implements GetAllVariantsQueryHandlerInterface
{
    public function handle(GetAllVariantsQuery $query): array
    {
        $variants = VariantModel::query()->with('powers')->orderBy('name')->get();

        return $variants->map(fn (VariantModel $variantModel) => new VariantDto(
            Id::fromString($variantModel->id),
            $variantModel->name,
            $variantModel->description,
            $variantModel->powers->map(fn ($power) => new VariantPowerDto(
                Id::fromString($power->id),
                $power->name,
            ))->toArray(),
        ))->toArray();
    }
}
