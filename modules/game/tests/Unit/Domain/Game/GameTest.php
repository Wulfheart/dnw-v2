<?php

namespace Dnw\Game\Tests\Unit\Domain\Game;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Core\Domain\Game\Game;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\AdjudicationTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\NoAdjudicationWeekdayCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\PhaseLength;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameName;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\JoinLength;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\GameVariantData;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Tests\Factory\AdjudicationTimingFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Game::class)]
class GameTest extends TestCase {
    public function test_create_random_assignment(): void
    {
        $gameVariantData = new GameVariantData(
            VariantId::new(),
            VariantPowerIdCollection::build(VariantPowerId::new(), VariantPowerId::new())

        );
        $game = Game::create(
            GameId::new(),
            GameName::fromString("Game Name"),
            AdjudicationTimingFactory::factory(),
            new GameStartTiming(
                new CarbonImmutable(),
                JoinLength::fromDays(4),
                true
            ),
            ,

        );
    }
}
