<?php

namespace Dnw\Game\Database\Seeders;

use Dnw\Game\Core\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use Illuminate\Database\Seeder;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $variant = VariantFactory::standard();
        $colonial = VariantFactory::colonial();

        $variantRepo = app(VariantRepositoryInterface::class);
        $variantRepo->save($variant);
        $variantRepo->save($colonial);
    }
}
