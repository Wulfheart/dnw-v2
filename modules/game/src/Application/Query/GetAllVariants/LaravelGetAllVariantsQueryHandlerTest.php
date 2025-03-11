<?php

namespace Dnw\Game\Application\Query\GetAllVariants;

use Dnw\Game\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Domain\Variant\Repository\Impl\LaravelVariantRepository\LaravelVariantRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\ModuleTestCase;

#[CoversClass(LaravelGetAllVariantsQueryHandler::class)]
class LaravelGetAllVariantsQueryHandlerTest extends ModuleTestCase
{
    public function test_retrieving_of_variants(): void
    {
        $repository = $this->bootstrap(LaravelVariantRepository::class);
        $repository->save(VariantFactory::standard());
        $repository->save(VariantFactory::colonial());

        $query = new GetAllVariantsQuery();
        $result = $this->bus->handle($query);

        $this->assertCount(2, $result->variants);
        $this->assertEquals('Colonial', $result->variants[0]->name);
    }

    public function test_retrieving_does_not_error_if_no_variant_is_present(): void
    {
        $query = new GetAllVariantsQuery();
        $result = $this->bus->handle($query);

        $this->assertEmpty($result->variants);
    }
}
