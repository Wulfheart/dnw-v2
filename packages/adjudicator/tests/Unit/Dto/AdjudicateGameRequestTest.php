<?php

namespace Dnw\Adjudicator\Test\Unit\Dto;

use Dnw\Adjudicator\Dto\AdjudicateGameRequest;
use Dnw\Adjudicator\Dto\Order;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AdjudicateGameRequest::class)]
#[CoversClass(Order::class)]
class AdjudicateGameRequestTest extends TestCase
{
    public function test(): void
    {
        /** @var string $file */
        $file = file_get_contents(__DIR__ . '/Fixtures/adjudicationRequest.json');

        /** @var array<mixed> $data */
        $data = json_decode($file, true);

        $adjudicateGameRequest = AdjudicateGameRequest::fromArray($data);

        /** @var string $encoded */
        $encoded = json_encode($adjudicateGameRequest);
        $this->assertJsonStringEqualsJsonString($file, $encoded);
    }
}
