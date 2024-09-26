<?php

namespace Dnw\Game\Core\Domain\Game\Entity;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wulfheart\Option\Option;

#[CoversClass(Phase::class)]
class PhaseTest extends TestCase
{
    /**
     * @param  Option<DateTime>  $adjudicationTime
     */
    #[DataProvider('adjudicationTimeIsExpiredDataProvider')]
    public function test_adjudicationTimeIsExpired(DateTime $currentTime, Option $adjudicationTime, bool $expectedResult): void
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
     * @return array<string, array{0:DateTime, 1: Option<DateTime>, 2: bool}>
     */
    public static function adjudicationTimeIsExpiredDataProvider(): array
    {
        $currentTime = new DateTime('now');

        return [
            'not expired if is not set' => [
                $currentTime,
                Option::none(),
                false,
            ],
            'not expired if is in the future' => [
                $currentTime,
                Option::some(new DateTime('tomorrow')),
                false,
            ],
            'not expired if is now' => [
                $currentTime,
                Option::some($currentTime),
                false,
            ],
            'expired if is in the past' => [
                $currentTime,
                Option::some(new DateTime('yesterday')),
                true,
            ],
        ];
    }
}
