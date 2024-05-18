<?php

namespace Dnw\Game\Tests\Unit\Domain\Game\Entity;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Game\Entity\Phase;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Std\Option;

#[CoversClass(Phase::class)]
class PhaseTest extends TestCase
{
    /**
     * @param  Option<CarbonImmutable>  $adjudicationTime
     */
    #[DataProvider('adjudicationTimeIsExpiredDataProvider')]
    public function test_adjudicationTimeIsExpired(CarbonImmutable $currentTime, Option $adjudicationTime, bool $expectedResult): void
    {
        $phase = new Phase(
            PhaseId::new(),
            PhaseTypeEnum::MOVEMENT,
            $adjudicationTime
        );

        $isExpired = $phase->adjudicationTimeIsExpired($currentTime);
        $this->assertEquals($expectedResult, $isExpired);
    }

    /**
     * @return array<string, array{0:CarbonImmutable, 1: Option<CarbonImmutable>, 2: bool}>
     */
    public static function adjudicationTimeIsExpiredDataProvider(): array
    {
        $currentTime = new CarbonImmutable('now');

        return [
            'not expired if is not set' => [
                $currentTime,
                Option::none(),
                false,
            ],
            'not expired if is in the future' => [
                $currentTime,
                Option::some(new CarbonImmutable('tomorrow')),
                false,
            ],
            'not expired if is now' => [
                $currentTime,
                Option::some($currentTime),
                false,
            ],
            'expired if is in the past' => [
                $currentTime,
                Option::some(new CarbonImmutable('yesterday')),
                true,
            ],
        ];
    }
}
