<?php

namespace Dnw\Game\Database\Seeders;

use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Tests\Factory\VariantFactory;
use Illuminate\Database\Seeder;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $variant = VariantFactory::standard();

        $variantRepo = app(VariantRepositoryInterface::class);
        $variantRepo->save($variant);
    }
}
