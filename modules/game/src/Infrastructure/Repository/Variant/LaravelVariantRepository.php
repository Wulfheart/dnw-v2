<?php

namespace Dnw\Game\Infrastructure\Repository\Variant;

use Dnw\Game\Domain\Game\ValueObject\Color;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Variant\Collection\VariantPowerCollection;
use Dnw\Game\Domain\Variant\Entity\VariantPower;
use Dnw\Game\Domain\Variant\Repository\LoadVariantResult;
use Dnw\Game\Domain\Variant\Repository\SaveVariantResult;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Domain\Variant\Shared\VariantId;
use Dnw\Game\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Domain\Variant\ValueObject\VariantApiName;
use Dnw\Game\Domain\Variant\ValueObject\VariantDescription;
use Dnw\Game\Domain\Variant\ValueObject\VariantName;
use Dnw\Game\Domain\Variant\ValueObject\VariantPower\VariantPowerApiName;
use Dnw\Game\Domain\Variant\ValueObject\VariantPower\VariantPowerName;
use Dnw\Game\Domain\Variant\Variant;
use Dnw\Game\Infrastructure\Model\Variant\VariantModel;
use Dnw\Game\Infrastructure\Model\Variant\VariantPowerModel;
use Illuminate\Database\DatabaseManager;

class LaravelVariantRepository implements VariantRepositoryInterface
{
    public function __construct(
        private DatabaseManager $databaseManager
    ) {}

    public function load(VariantId $variantId): LoadVariantResult
    {
        $variantModel = VariantModel::with('powers')->find(
            (string) $variantId,
        );

        if ($variantModel === null) {
            return LoadVariantResult::err(LoadVariantResult::E_VARIANT_NOT_FOUND);
        }

        $variantPowerCollection = new VariantPowerCollection(
            $variantModel->powers->map(fn (VariantPowerModel $power) => new VariantPower(
                VariantPowerId::fromString($power->id),
                VariantPowerName::fromString($power->name),
                VariantPowerApiName::fromString($power->api_name),
                Color::fromString($power->color)
            ))->toArray()
        );

        $variant =  new Variant(
            VariantId::fromString($variantModel->id),
            VariantName::fromString($variantModel->name),
            VariantApiName::fromString($variantModel->api_name),
            VariantDescription::fromString($variantModel->description),
            $variantPowerCollection,
            Count::fromInt($variantModel->default_supply_centers_to_win_count),
            Count::fromInt($variantModel->total_supply_center_count)
        );

        return LoadVariantResult::ok($variant);
    }

    public function save(Variant $variant): SaveVariantResult
    {
        $this->databaseManager->transaction(function () use ($variant) {
            VariantModel::query()->updateOrCreate(
                ['id' => (string) $variant->id],
                [
                    'name' => $variant->name,
                    'api_name' => $variant->apiName,
                    'description' => $variant->description,
                    'default_supply_centers_to_win_count' => $variant->defaultSupplyCentersToWinCount->int(),
                    'total_supply_center_count' => $variant->totalSupplyCentersCount->int(),
                ]
            );

            foreach ($variant->variantPowerCollection as $variantPower) {
                VariantPowerModel::query()->updateOrCreate(
                    ['id' => (string) $variantPower->id],
                    [
                        'variant_id' => (string) $variant->id,
                        'name' => $variantPower->name,
                        'api_name' => $variantPower->apiName,
                        'color' => (string) $variantPower->color,
                    ]
                );
            }

        });

        return SaveVariantResult::ok();
    }
}
