<?php

namespace Dnw\Game\Domain\Variant\Repository;

use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use Dnw\Game\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Domain\Variant\Shared\VariantId;
use Tests\LaravelTestCase;

#[AllowLaravelTestCase]
abstract class AbstractVariantRepositoryTestCase extends LaravelTestCase
{
    abstract public function buildRepository(): VariantRepositoryInterface;

    public function test_load_and_save(): void
    {
        $repository = $this->buildRepository();
        $variant = VariantFactory::standard();
        $saveResult = $repository->save($variant);
        $this->assertTrue($saveResult->isOk());

        $loadedVariant = $repository->load($variant->id);
        $this->assertEquals($variant, $loadedVariant->unwrap());
    }

    public function test_load_not_found(): void
    {
        $repository = $this->buildRepository();
        $result = $repository->load(VariantId::new());
        $this->assertTrue($result->hasErr());
        $this->assertEquals(LoadVariantResult::E_VARIANT_NOT_FOUND, $result->unwrapErr());
    }
}
