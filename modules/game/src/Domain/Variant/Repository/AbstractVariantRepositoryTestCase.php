<?php

namespace Dnw\Game\Domain\Variant\Repository;

use Dnw\Game\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Domain\Variant\Shared\VariantKey;
use Tests\ModuleTestCase;

abstract class AbstractVariantRepositoryTestCase extends ModuleTestCase
{
    abstract public function buildRepository(): VariantRepositoryInterface;

    public function test_load_and_save(): void
    {
        $repository = $this->buildRepository();
        $variant = VariantFactory::standard();
        $saveResult = $repository->save($variant);
        $this->assertTrue($saveResult->isOk());

        $loadedVariant = $repository->load($variant->key->clone());
        $this->assertEquals($variant, $loadedVariant->unwrap());
    }

    public function test_load_not_found(): void
    {
        $repository = $this->buildRepository();
        $result = $repository->load(VariantKey::fromString('::NON_EXISTENT::'));
        $this->assertTrue($result->isErr());
        $this->assertEquals(LoadVariantResult::E_VARIANT_NOT_FOUND, $result->unwrapErr());
    }

    public function test_keyExists(): void
    {
        $repository = $this->buildRepository();
        $variant = VariantFactory::standard();
        $repository->save($variant);

        $this->assertTrue($repository->keyExists($variant->key->clone()));
        $this->assertFalse($repository->keyExists(VariantKey::fromString('::NON_EXISTENT::')));

    }
}
