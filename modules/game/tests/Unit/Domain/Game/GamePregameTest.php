<?php

namespace Dnw\Game\Tests\Unit\Domain\Game;

use Carbon\CarbonImmutable;
use Dnw\Foundation\Exception\DomainException;
use Dnw\Game\Core\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Core\Domain\Game\Event\GameAbandonedEvent;
use Dnw\Game\Core\Domain\Game\Event\GameCreatedEvent;
use Dnw\Game\Core\Domain\Game\Event\GameJoinTimeExceededEvent;
use Dnw\Game\Core\Domain\Game\Event\GameStartedEvent;
use Dnw\Game\Core\Domain\Game\Event\PlayerJoinedEvent;
use Dnw\Game\Core\Domain\Game\Event\PlayerLeftEvent;
use Dnw\Game\Core\Domain\Game\Game;
use Dnw\Game\Core\Domain\Game\Rule\GameRules;
use Dnw\Game\Core\Domain\Game\StateMachine\GameStates;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameName;
use Dnw\Game\Core\Domain\Game\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\GameVariantData;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Tests\Asserter\GameAsserter;
use Dnw\Game\Tests\Factory\AdjudicationTimingFactory;
use Dnw\Game\Tests\Factory\GameBuilder;
use Dnw\Game\Tests\Factory\GameStartTimingFactory;
use Exception;
use PhpOption\None;
use PhpOption\Some;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(Game::class)]
class GamePregameTest extends TestCase
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

        GameAsserter::assertThat($game)
            ->hasEvent(GameCreatedEvent::class)
            ->hasState(GameStates::CREATED);
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

        GameAsserter::assertThat($game)
            ->hasEvent(GameCreatedEvent::class)
            ->hasState(GameStates::CREATED);
    }

    public function test_canJoin_player_already_joined(): void
    {
        $game = GameBuilder::initialize(true)->storeInitialAdjudication()->join()->build();
        $ruleset = $game->canJoin(
            $game->powerCollection->findBy(fn ($power) => $power->playerId->isDefined())->get()->playerId->get(),
            None::create(),
            new CarbonImmutable(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::PLAYER_ALREADY_JOINED));
    }

    public function test_canJoin_if_desired_power_is_already_filled(): void
    {
        $game = GameBuilder::initialize(false)->storeInitialAdjudication()->join()->build();
        $ruleset = $game->canJoin(
            PlayerId::new(),
            Some::create($game->powerCollection->findBy(fn ($power) => $power->playerId->isDefined())->get()->variantPowerId),
            new CarbonImmutable(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::POWER_ALREADY_FILLED));
    }

    public function test_canJoin_if_state_is_incorrect(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();
        $ruleset = $game->canJoin(
            PlayerId::new(),
            None::create(),
            new CarbonImmutable(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::EXPECTS_STATE_PLAYERS_JOINING));
    }

    public function test_canJoin_fails_if_join_length_is_exceeded(): void
    {
        $game = GameBuilder::initialize(true, false)->storeInitialAdjudication()->fillUntilOnePowerLeft()->build();

        $startTiming = $game->gameStartTiming;
        $currentTime = $startTiming->startOfJoinPhase->addDays($startTiming->joinLength->toDays())->addMinutes(1);

        $ruleset = $game->canJoin(
            PlayerId::new(),
            None::create(),
            $currentTime,
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::JOIN_LENGTH_IS_EXCEEDED));
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

        GameAsserter::assertThat($game)
            ->hasState(GameStates::PLAYERS_JOINING)
            ->hasEvent(PlayerJoinedEvent::class);
    }

    public function test_join_with_non_random_assignments(): void
    {
        $playerId = PlayerId::new();
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $variantPowerId = $game->powerCollection->getUnassignedPowers()->getOffset(0)->variantPowerId;

        $game->join($playerId, Some::create($variantPowerId), new CarbonImmutable(), fn () => 1);

        $this->assertTrue($game->powerCollection->getByPlayerId($playerId)->playerId->isDefined());

        GameAsserter::assertThat($game)
            ->hasState(GameStates::PLAYERS_JOINING)
            ->hasEvent(PlayerJoinedEvent::class);
    }

    public function test_join_starts_the_game_if_start_when_ready_is_selected(): void
    {
        $game = GameBuilder::initialize(true, true)->storeInitialAdjudication()->fillUntilOnePowerLeft()->build();

        $game->join(PlayerId::new(), None::create(), new CarbonImmutable(), fn () => 0);

        GameAsserter::assertThat($game)
            ->hasState(GameStates::ORDER_SUBMISSION)
            ->hasEvent(GameStartedEvent::class);
        $this->assertTrue($game->phasesInfo->currentPhase->get()->adjudicationTime->isDefined());
    }

    public function test_join_throws_exception_if_it_is_not_possible_to_join_the_game(): void
    {
        $game = GameBuilder::initialize(true)->storeInitialAdjudication()->makeFull()->build();

        $this->expectException(DomainException::class);
        $game->join(PlayerId::new(), None::create(), new CarbonImmutable(), fn () => 0);

    }

    public function test_canLeave_player_is_not_in_game(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $ruleset = $game->canLeave(
            PlayerId::new(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::PLAYER_NOT_IN_GAME));
    }

    public function test_canLeave_has_unexpected_state(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();
        $ruleset = $game->canLeave(
            $game->powerCollection->findBy(fn ($power) => $power->playerId->isDefined())->get()->playerId->get(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::EXPECTS_STATE_PLAYERS_JOINING));
    }

    public function test_leave_throws_exception_if_player_cannot_leave(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();

        $this->expectException(DomainException::class);
        $game->leave(
            PlayerId::new(),
        );
    }

    public function test_leave_removes_player_from_game(): void
    {
        $playerId = PlayerId::new();
        $game = GameBuilder::initialize()->storeInitialAdjudication()->join($playerId)->build();

        $game->leave($playerId);

        $this->assertTrue($game->powerCollection->doesNotContainPlayer($playerId));
        GameAsserter::assertThat($game)
            ->hasState(GameStates::PLAYERS_JOINING)
            ->hasEvent(PlayerLeftEvent::class)
            ->hasNotEvent(GameAbandonedEvent::class);
    }

    public function test_leave_sends_GameAbandonedEvent_if_no_players_are_left(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();

        $game->leave($game->powerCollection->findBy(fn ($power) => $power->playerId->isDefined())->get()->playerId->get());

        GameAsserter::assertThat($game)
            ->hasState(GameStates::ABANDONED)
            ->hasEvent(PlayerLeftEvent::class)
            ->hasEvent(GameAbandonedEvent::class);
    }

    public function test_handleGameJoinLengthExceeded_starts_game_if_time_is_right_and_game_full(): void
    {
        $game = GameBuilder::initialize(startWhenReady: false)->storeInitialAdjudication()->makeFull()->build();

        $currentTime = $game->gameStartTiming->startOfJoinPhase->addDays($game->gameStartTiming->joinLength->toDays());

        $game->handleGameJoinLengthExceeded($currentTime->addMinute());

        GameAsserter::assertThat($game)
            ->hasState(GameStates::ORDER_SUBMISSION)
            ->hasEvent(GameStartedEvent::class);

        $this->assertTrue($game->phasesInfo->currentPhase->get()->adjudicationTime->isDefined());
    }

    public function test_handleGameJoinLengthExceeded_does_not_start_game_if_time_is_not_right_and_game_full(): void
    {
        $game = GameBuilder::initialize(startWhenReady: false)->storeInitialAdjudication()->makeFull()->build();

        $game->handleGameJoinLengthExceeded(new CarbonImmutable());

        GameAsserter::assertThat($game)
            ->hasState(GameStates::PLAYERS_JOINING)
            ->hasNotEvent(GameStartedEvent::class);

        $this->assertTrue($game->phasesInfo->currentPhase->get()->adjudicationTime->isEmpty());
    }

    public function test_handleGameJoinLengthExceeded_abandons_game_if_join_length_is_exceeded_and_game_not_full(): void
    {
        $game = GameBuilder::initialize(startWhenReady: false)->storeInitialAdjudication()->build();

        $currentTime = $game->gameStartTiming->startOfJoinPhase->addDays($game->gameStartTiming->joinLength->toDays());

        $game->handleGameJoinLengthExceeded($currentTime->addMinute());

        GameAsserter::assertThat($game)
            ->hasState(GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE)
            ->hasEvent(GameJoinTimeExceededEvent::class);
    }
}
