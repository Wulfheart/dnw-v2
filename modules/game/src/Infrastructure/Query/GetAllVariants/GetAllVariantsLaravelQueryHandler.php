<?php

namespace Dnw\Game\Infrastructure\Query\GetAllVariants;

use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsQuery;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsQueryHandlerInterface;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsQueryResult;
use Dnw\Game\Application\Query\GetAllVariants\VariantDto;
use Dnw\Game\Application\Query\GetAllVariants\VariantPowerDto;
use Dnw\Game\Infrastructure\Model\Variant\VariantModel;

class GetAllVariantsLaravelQueryHandler implements GetAllVariantsQueryHandlerInterface
{
    public function handle(GetAllVariantsQuery $query): GetAllVariantsQueryResult
    {
        $variants = VariantModel::query()->with('powers')->orderBy('name')->get();

        return new GetAllVariantsQueryResult(
            $variants->map(fn (VariantModel $variantModel) => new VariantDto(
                $variantModel->id,
                $variantModel->name,
                $variantModel->description,
                $variantModel->powers->map(fn ($power) => new VariantPowerDto(
                    $power->id,
                    $power->name,
                ))->toArray(),
            ))->toArray()
        );
    }
}
