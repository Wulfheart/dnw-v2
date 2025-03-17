<?php

namespace Dnw\Game\Domain\Variant\Repository\Impl\LaravelVariantRepository;

use Dnw\Game\Domain\Game\ValueObject\Color;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Variant\Collection\VariantPowerCollection;
use Dnw\Game\Domain\Variant\Entity\VariantPower;
use Dnw\Game\Domain\Variant\Repository\LoadVariantResult;
use Dnw\Game\Domain\Variant\Repository\SaveVariantResult;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Domain\Variant\Shared\VariantKey;
use Dnw\Game\Domain\Variant\Shared\VariantPowerKey;
use Dnw\Game\Domain\Variant\ValueObject\VariantDescription;
use Dnw\Game\Domain\Variant\ValueObject\VariantName;
use Dnw\Game\Domain\Variant\ValueObject\VariantPower\VariantPowerName;
use Dnw\Game\Domain\Variant\Variant;
use Illuminate\Database\DatabaseManager;

class LaravelVariantRepository implements VariantRepositoryInterface
{
    public function __construct(
        private DatabaseManager $databaseManager
    ) {}

    public function load(VariantKey $variantKey): LoadVariantResult
    {
        $variantModel = VariantModel::with('powers')->firstWhere(
            'key',
            (string) $variantKey,
        );

        if ($variantModel === null) {
            return LoadVariantResult::err(LoadVariantResult::E_VARIANT_NOT_FOUND);
        }

        $variant = $this->translateFromModel($variantModel);

        return LoadVariantResult::ok($variant);
    }

    private function translateFromModel(VariantModel $variantModel): Variant
    {
        $variantPowerCollection = new VariantPowerCollection(
            $variantModel->powers->map(fn (VariantPowerModel $power) => new VariantPower(
                VariantPowerKey::fromString($power->key),
                VariantPowerName::fromString($power->name),
                Color::fromString($power->color)
            ))->toArray()
        );

        return new Variant(
            VariantKey::fromString($variantModel->key),
            VariantName::fromString($variantModel->name),
            VariantDescription::fromString($variantModel->description),
            $variantPowerCollection,
            Count::fromInt($variantModel->default_supply_centers_to_win_count),
            Count::fromInt($variantModel->total_supply_center_count)
        );
    }

    public function keyExists(VariantKey $variantKey): bool
    {
        return VariantModel::query()->where('key', (string) $variantKey)->exists();
    }

    public function save(Variant $variant): SaveVariantResult
    {
        $this->databaseManager->transaction(function () use ($variant) {
            VariantModel::query()->updateOrCreate(
                ['key' => (string) $variant->key],
                [
                    'name' => $variant->name,
                    'description' => $variant->description,
                    'default_supply_centers_to_win_count' => $variant->defaultSupplyCentersToWinCount->int(),
                    'total_supply_center_count' => $variant->totalSupplyCentersCount->int(),
                ]
            );

            foreach ($variant->variantPowerCollection as $variantPower) {
                $result = VariantPowerModel::query()->updateOrCreate(
                    [
                        'key' => (string) $variantPower->key,
                        'variant_key' => (string) $variant->key,
                    ],
                    [
                        'name' => $variantPower->name,
                        'color' => (string) $variantPower->color,
                    ]
                );

                $d = $result->toArray();

                $x = 1;
            }

        });

        return SaveVariantResult::ok();
    }
}
