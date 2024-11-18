<?php

namespace Database\Seeders;

use Dnw\Game\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\User\Infrastructure\UserModel;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        UserModel::factory()->count(10)->create();

        $variant = VariantFactory::standard();
        $colonial = VariantFactory::colonial();

        $variantRepo = app(VariantRepositoryInterface::class);
        $variantRepo->save($variant);
        $variantRepo->save($colonial);
    }
}
