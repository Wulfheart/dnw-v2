<?php

namespace Dnw\Game\Tests\Unit\Domain\Game\Entity;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Game\Entity\Phase;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

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
            PhaseId::generate(),
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
                None::create(),
                false,
            ],
            'not expired if is in the future' => [
                $currentTime,
                Some::create(new CarbonImmutable('tomorrow')),
                false,
            ],
            'not expired if is now' => [
                $currentTime,
                Some::create($currentTime),
                false,
            ],
            'expired if is in the past' => [
                $currentTime,
                Some::create(new CarbonImmutable('yesterday')),
                true,
            ],
        ];
    }
}
