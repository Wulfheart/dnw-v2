<?php

namespace Dnw\Adjudicator\Test\Unit\Dto;

use Dnw\Adjudicator\Dto\AdjudicateGameResponse;
use Dnw\Adjudicator\Dto\AppliedOrder;
use Dnw\Adjudicator\Dto\PhasePowerData;
use Dnw\Adjudicator\Dto\PossibleOrder;
use Dnw\Adjudicator\Dto\Unit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AdjudicateGameResponse::class)]
#[CoversClass(AppliedOrder::class)]
#[CoversClass(PhasePowerData::class)]
#[CoversClass(PossibleOrder::class)]
#[CoversClass(Unit::class)]
class AdjudicateGameResponseTest extends TestCase
{
    public function test(): void
    {
        /** @var string $file */
        $file = file_get_contents(__DIR__ . '/Fixtures/adjudicationResponse.json');

        /** @var array<mixed> $data */
        $data = json_decode($file, true);

        $adjudicateGameRequest = AdjudicateGameResponse::fromArray($data);

        /** @var string $encoded */
        $encoded = json_encode($adjudicateGameRequest);
        $this->assertJsonStringEqualsJsonString($file, $encoded);
    }
}
