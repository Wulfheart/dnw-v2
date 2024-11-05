<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Dnw\Game\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->count(10)->create();

        $variant = VariantFactory::standard();
        $colonial = VariantFactory::colonial();

        $variantRepo = app(VariantRepositoryInterface::class);
        $variantRepo->save($variant);
        $variantRepo->save($colonial);
    }
}
