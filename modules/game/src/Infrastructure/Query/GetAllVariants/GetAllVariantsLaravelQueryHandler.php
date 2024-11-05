<?php

namespace Dnw\Game\Infrastructure\Query\GetAllVariants;

use Dnw\Foundation\Identity\Id;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsQuery;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsQueryHandlerInterface;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsResult;
use Dnw\Game\Application\Query\GetAllVariants\VariantDto;
use Dnw\Game\Application\Query\GetAllVariants\VariantPowerDto;
use Dnw\Game\Infrastructure\Model\Variant\VariantModel;

class GetAllVariantsLaravelQueryHandler implements GetAllVariantsQueryHandlerInterface
{
    public function handle(GetAllVariantsQuery $query): GetAllVariantsResult
    {
        $variants = VariantModel::query()->with('powers')->orderBy('name')->get();

        return new GetAllVariantsResult(
            $variants->map(fn (VariantModel $variantModel) => new VariantDto(
                Id::fromString($variantModel->id),
                $variantModel->name,
                $variantModel->description,
                $variantModel->powers->map(fn ($power) => new VariantPowerDto(
                    Id::fromString($power->id),
                    $power->name,
                ))->toArray(),
            ))->toArray()
        );
    }
}
