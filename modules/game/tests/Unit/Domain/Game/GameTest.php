<?php

namespace Dnw\Game\Tests\Unit\Domain\Game;

use Carbon\CarbonImmutable;
use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Game\Core\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Core\Domain\Game\Event\GameJoinTimeExceededEvent;
use Dnw\Game\Core\Domain\Game\Event\GameStartedEvent;
use Dnw\Game\Core\Domain\Game\Event\PlayerJoinedEvent;
use Dnw\Game\Core\Domain\Game\Game;
use Dnw\Game\Core\Domain\Game\Rule\GameRules;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameName;
use Dnw\Game\Core\Domain\Game\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\GameVariantData;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Tests\Factory\AdjudicationTimingFactory;
use Dnw\Game\Tests\Factory\GameBuilder;
use Dnw\Game\Tests\Factory\GameStartTimingFactory;
use Exception;
use PhpOption\None;
use PhpOption\Some;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Game::class)]
class GameTest extends TestCase
{
    public function test_create_random_assignment(): void
    {
        $firstId = VariantPowerId::new();
        $secondId = VariantPowerId::new();
        $gameVariantData = new GameVariantData(
            VariantId::new(),
            VariantPowerIdCollection::build($firstId, $secondId),
            Count::fromInt(4)
        );

        $playerId = PlayerId::new();

        $game = Game::create(
            GameId::new(),
            GameName::fromString('Game Name'),
            AdjudicationTimingFactory::build(),
            GameStartTimingFactory::build(),
            $gameVariantData,
            true,
            $playerId,
            None::create(),
            fn () => 1
        );

        $this->assertCount(2, $game->powerCollection);
        $this->assertEquals($secondId, $game->powerCollection->getByPlayerId($playerId)->variantPowerId);
    }

    public function test_create_non_random_assignment(): void
    {
        $firstId = VariantPowerId::new();
        $secondId = VariantPowerId::new();
        $gameVariantData = new GameVariantData(
            VariantId::new(),
            VariantPowerIdCollection::build($firstId, $secondId),
            Count::fromInt(4)
        );

        $playerId = PlayerId::new();

        $game = Game::create(
            GameId::new(),
            GameName::fromString('Game Name'),
            AdjudicationTimingFactory::build(),
            GameStartTimingFactory::build(),
            $gameVariantData,
            false,
            $playerId,
            Some::create($secondId),
            fn () => throw new Exception('Should not be called')
        );

        $this->assertCount(2, $game->powerCollection);
        $this->assertEquals($secondId, $game->powerCollection->getByPlayerId($playerId)->variantPowerId);
    }

    #[DataProvider('canJoinRuleBreakerProvider')]
    public function test_canJoin(string $key, GameBuilder $game): void
    {
        $game = $game->build();
        $ruleset = $game->canJoin(
            PlayerId::new(),
            None::create(),
        );

        $this->assertTrue($ruleset->containsViolation($key));
    }

    /**
     * @return array<string, array{0: string, 1: GameBuilder}>
     */
    public static function canJoinRuleBreakerProvider(): array
    {
        return [
            'game already started' => [
                GameRules::HAS_BEEN_STARTED,
                GameBuilder::initialize(true)->storeInitialAdjudication()->start(),
            ],
            'initial phase does not exist' => [
                GameRules::INITIAL_PHASE_DOES_NOT_EXIST,
                GameBuilder::initialize(true),
            ],
            'no available powers' => [
                GameRules::HAS_NO_AVAILABLE_POWERS,
                GameBuilder::initialize(true)->storeInitialAdjudication()->makeFull(),
            ],
            'abandoned' => [
                GameRules::HAS_BEEN_ABANDONED,
                GameBuilder::initialize(true)->storeInitialAdjudication()->join()->abandon(),
            ],
        ];
    }

    public function test_canJoin_player_already_joined(): void
    {
        $game = GameBuilder::initialize(true)->storeInitialAdjudication()->join()->build();
        $ruleset = $game->canJoin(
            $game->powerCollection->findBy(fn ($power) => $power->playerId->isDefined())->get()->playerId->get(),
            None::create(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::PLAYER_ALREADY_JOINED));
    }

    public function test_canJoin_if_desired_power_is_already_filled(): void
    {
        $game = GameBuilder::initialize(false)->storeInitialAdjudication()->join()->build();
        $ruleset = $game->canJoin(
            PlayerId::new(),
            Some::create($game->powerCollection->findBy(fn ($power) => $power->playerId->isDefined())->get()->variantPowerId),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::POWER_ALREADY_FILLED));
    }

    public function test_join_with_random_assignments(): void
    {
        $playerId = PlayerId::new();
        $game = GameBuilder::initialize(true)->storeInitialAdjudication()->join()->build();

        $expectedVariantPowerId = $game->powerCollection->getUnassignedPowers()->getOffset(1)->variantPowerId;

        $game->join($playerId, None::create(), new CarbonImmutable(), fn () => 1);

        $this->assertEquals(
            $expectedVariantPowerId,
            $game->powerCollection->getByPlayerId($playerId)->variantPowerId
        );
        $this->assertEventsContain($game, PlayerJoinedEvent::class);
    }

    public function test_join_with_non_random_assignments(): void
    {
        $playerId = PlayerId::new();
        $game = GameBuilder::initialize()->storeInitialAdjudication()->join($playerId)->build();

        $this->assertTrue($game->powerCollection->getByPlayerId($playerId)->playerId->isDefined());
        $this->assertEventsContain($game, PlayerJoinedEvent::class);
    }

    public function test_join_starts_the_game_if_the_join_length_is_exceeded(): void
    {
        $game = GameBuilder::initialize(true, false)->storeInitialAdjudication()->fillUntilOnePowerLeft()->build();

        $startTiming = $game->gameStartTiming;
        $currentTime = $startTiming->startOfJoinPhase->addDays($startTiming->joinLength->toDays())->addMinutes(1);

        $game->join(PlayerId::new(), None::create(), $currentTime, fn () => 0);

        $this->assertEventsContain($game, GameStartedEvent::class);
        $this->assertTrue($game->phasesInfo->currentPhase->get()->adjudicationTime->isDefined());

    }

    public function test_join_starts_the_game_if_start_when_ready_is_selected(): void
    {
        $game = GameBuilder::initialize(true, true)->storeInitialAdjudication()->fillUntilOnePowerLeft()->build();

        $game->join(PlayerId::new(), None::create(), new CarbonImmutable(), fn () => 0);

        $this->assertEventsContain($game, GameStartedEvent::class);
        $this->assertTrue($game->phasesInfo->currentPhase->get()->adjudicationTime->isDefined());
    }

    public function test_join_sends_event_if_join_length_is_exceeded(): void
    {
        $game = GameBuilder::initialize(true, false)->storeInitialAdjudication()->build();

        $startTiming = $game->gameStartTiming;
        $currentTime = $startTiming->startOfJoinPhase->addDays($startTiming->joinLength->toDays())->addMinutes(1);

        $game->join(PlayerId::new(), None::create(), $currentTime, fn () => 0);

        $this->assertEventsContain($game, GameJoinTimeExceededEvent::class);
    }

    private function assertEventsContain(Game $game, string $eventName): void
    {
        $result = ArrayCollection::build(...$game->releaseEvents())->findBy(fn ($event) => $event::class === $eventName);
        $this->assertTrue($result->isDefined(), "Event $eventName not found in game events.");
    }
}
