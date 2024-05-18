<?php

namespace Dnw\Game\Tests\Unit\Infrastructure\Repository\Variant;

use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Core\Infrastructure\Repository\Variant\LaravelVariantRepository;
use Dnw\Game\Tests\Unit\Domain\Variant\Repository\AbstractVariantRepositoryTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LaravelVariantRepository::class)]
class LaravelVariantRepositoryTest extends AbstractVariantRepositoryTestCase
{
    public function buildRepository(): VariantRepositoryInterface
    {
        return $this->app->make(LaravelVariantRepository::class);
    }
}
