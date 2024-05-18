<?php

namespace Dnw\Game\Tests\Unit\Domain\Variant\Repository;

use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Tests\Factory\VariantFactory;
use Tests\TestCase;

abstract class AbstractVariantRepositoryTestCase extends TestCase
{
    abstract public function buildRepository(): VariantRepositoryInterface;

    public function test_load_and_save(): void
    {
        $repository = $this->buildRepository();
        $variant = VariantFactory::standard();
        $repository->save($variant);
        $loadedVariant = $repository->load($variant->id);
        $this->assertEquals($variant, $loadedVariant);
    }
}
