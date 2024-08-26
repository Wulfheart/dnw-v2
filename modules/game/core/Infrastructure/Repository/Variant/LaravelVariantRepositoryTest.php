<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Variant;

use Dnw\Game\Core\Domain\Variant\Repository\AbstractVariantRepositoryTestCase;
use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LaravelVariantRepository::class)]
class LaravelVariantRepositoryTest extends AbstractVariantRepositoryTestCase
{
    public function buildRepository(): VariantRepositoryInterface
    {
        return $this->app->make(LaravelVariantRepository::class);
    }
}
