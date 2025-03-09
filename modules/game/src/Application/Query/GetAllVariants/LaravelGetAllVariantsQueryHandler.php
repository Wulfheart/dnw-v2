<?php

namespace Dnw\Game\Application\Query\GetAllVariants;

use Dnw\Game\Domain\Variant\Repository\Impl\LaravelVariantRepository\VariantModel;

class LaravelGetAllVariantsQueryHandler implements GetAllVariantsQueryHandlerInterface
{
    public function handle(GetAllVariantsQuery $query): GetAllVariantsQueryResult
    {
        $variants = VariantModel::query()->with('powers')->orderBy('name')->get();

        return new GetAllVariantsQueryResult(
            $variants->map(fn (VariantModel $variantModel) => new VariantDto(
                $variantModel->key,
                $variantModel->name,
                $variantModel->description,
                $variantModel->powers->map(fn ($power) => new VariantPowerDto(
                    $power->key,
                    $power->name,
                ))->toArray(),
            ))->toArray()
        );
    }
}
