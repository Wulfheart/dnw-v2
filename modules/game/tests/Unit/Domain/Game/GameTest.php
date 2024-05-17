<?php

namespace Dnw\Game\Tests\Unit\Domain\Game;

use Carbon\CarbonImmutable;
use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\Exception\DomainException;
use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Core\Domain\Game\Entity\Power;
use Dnw\Game\Core\Domain\Game\Event\GameAbandonedEvent;
use Dnw\Game\Core\Domain\Game\Event\GameCreatedEvent;
use Dnw\Game\Core\Domain\Game\Event\GameReadyForAdjudicationEvent;
use Dnw\Game\Core\Domain\Game\Event\GameStartedEvent;
use Dnw\Game\Core\Domain\Game\Event\OrdersSubmittedEvent;
use Dnw\Game\Core\Domain\Game\Event\PlayerJoinedEvent;
use Dnw\Game\Core\Domain\Game\Event\PlayerLeftEvent;
use Dnw\Game\Core\Domain\Game\Game;
use Dnw\Game\Core\Domain\Game\Rule\GameRules;
use Dnw\Game\Core\Domain\Game\StateMachine\GameStates;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameName;
use Dnw\Game\Core\Domain\Game\ValueObject\Order\Order;
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
        $this->assertEventsContain($game, GameCreatedEvent::class);
        $this->assertEquals(GameStates::CREATED, $game->gameStateMachine->currentState());
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
        $this->assertEventsContain($game, GameCreatedEvent::class);
        $this->assertEquals(GameStates::CREATED, $game->gameStateMachine->currentState());
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
        $this->assertEventsContain($game, PlayerJoinedEvent::class);
    }

    public function test_join_with_non_random_assignments(): void
    {
        $playerId = PlayerId::new();
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $variantPowerId = $game->powerCollection->getUnassignedPowers()->getOffset(0)->variantPowerId;

        $game->join($playerId, Some::create($variantPowerId), new CarbonImmutable(), fn () => 1);

        $this->assertTrue($game->powerCollection->getByPlayerId($playerId)->playerId->isDefined());
        $this->assertEventsContain($game, PlayerJoinedEvent::class);
    }

    public function test_join_starts_the_game_if_start_when_ready_is_selected(): void
    {
        $game = GameBuilder::initialize(true, true)->storeInitialAdjudication()->fillUntilOnePowerLeft()->build();

        $game->join(PlayerId::new(), None::create(), new CarbonImmutable(), fn () => 0);

        $this->assertEventsContain($game, GameStartedEvent::class);
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
        $this->assertEventsContain($game, PlayerLeftEvent::class);
        $this->assertEventsDoNotContain($game, GameAbandonedEvent::class);
    }

    public function test_leave_sends_GameAbandonedEvent_if_no_players_are_left(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();

        $game->leave($game->powerCollection->findBy(fn ($power) => $power->playerId->isDefined())->get()->playerId->get());

        $this->assertEventsContain($game, PlayerLeftEvent::class);
        $this->assertEventsContain($game, GameAbandonedEvent::class);
    }

    public function test_canSubmitOrders_unexpected_status(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $ruleset = $game->canSubmitOrders(
            $game->powerCollection->findBy(fn ($power) => $power->playerId->isDefined())->get()->playerId->get(),
            new CarbonImmutable(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::EXPECTS_STATE_ORDER_SUBMISSION));
    }

    public function test_canSubmitOrders_player_is_not_in_game(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $ruleset = $game->canSubmitOrders(
            PlayerId::new(),
            new CarbonImmutable(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::PLAYER_NOT_IN_GAME));
    }

    public function test_canSubmitOrders_player_does_not_need_to_submit_orders(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();

        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->get();
        $powerToTest->currentPhaseData->get()->ordersNeeded = false;

        $ruleset = $game->canSubmitOrders(
            $powerToTest->playerId->get(),
            new CarbonImmutable(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::POWER_DOES_NOT_NEED_TO_SUBMIT_ORDERS));
    }

    public function test_canSubmitOrders_player_has_orders_already_marked_as_ready(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->markOnePowerAsReady()->build();
        $ruleset = $game->canSubmitOrders(
            $game->powerCollection->findBy(fn ($power) => $power->ordersMarkedAsReady())->get()->playerId->get(),
            new CarbonImmutable(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::ORDERS_ALREADY_MARKED_AS_READY));
    }

    public function test_canSubmitOrders_fails_if_adjudication_time_is_expired(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->markOnePowerAsReady()->build();
        $time = $game->phasesInfo->currentPhase->get()->adjudicationTime->get()->addMinute();
        $ruleset = $game->canSubmitOrders(
            $game->powerCollection->findBy(fn ($power) => $power->ordersMarkedAsReady())->get()->playerId->get(),
            $time,
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::GAME_PHASE_TIME_EXCEEDED));
    }

    public function test_submitOrders_throws_exception_if_orders_cannot_be_submitted(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $this->expectException(DomainException::class);
        $game->submitOrders(PlayerId::new(), new OrderCollection(), false, new CarbonImmutable());
    }

    public function test_submitOrders_throws_exception_if_the_orders_have_not_changed(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();
        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->get();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("Power $powerToTest->powerId cannot submit empty orders for game $game->gameId");
        $game->submitOrders($powerToTest->playerId->get(), new OrderCollection(), true, new CarbonImmutable());
    }

    public function test_submitOrders_does_not_allow_resubmission_of_the_exact_same_orders(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();
        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->get();

        $orders = OrderCollection::build(Order::fromString('A PAR - MAR'));

        $game->submitOrders($powerToTest->playerId->get(), $orders, false, new CarbonImmutable());

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("Power $powerToTest->powerId has already submitted orders exactly the same orders for game $game->gameId");
        $game->submitOrders($powerToTest->playerId->get(), $orders, false, new CarbonImmutable());

    }

    public function test_submitOrders_does_not_adjudicate_game_if_not_ready(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();
        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->get();

        $orders = OrderCollection::build(Order::fromString('A PAR - MAR'));

        $game->submitOrders($powerToTest->playerId->get(), $orders, true, new CarbonImmutable());

        $this->assertEquals($orders, $powerToTest->currentPhaseData->get()->orderCollection->get());
        $this->assertEventsContain($game, OrdersSubmittedEvent::class);
        $this->assertEventsDoNotContain($game, GameReadyForAdjudicationEvent::class);
        $this->assertEquals(GameStates::ORDER_SUBMISSION, $game->gameStateMachine->currentState());
    }

    public function test_submitOrders_adjudicates_game_if_ready(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->markAllButOnePowerAsReady()->build();
        $powerToTest = $game->powerCollection->findBy(fn (Power $power) => ! $power->ordersMarkedAsReady())->get();

        $orders = OrderCollection::build(Order::fromString('A PAR - MAR'));

        $game->submitOrders($powerToTest->playerId->get(), $orders, true, new CarbonImmutable());

        $this->assertEquals($orders, $powerToTest->currentPhaseData->get()->orderCollection->get());
        $this->assertEventsContain($game, OrdersSubmittedEvent::class);
        $this->assertEventsContain($game, GameReadyForAdjudicationEvent::class);
        $this->assertEquals(GameStates::ADJUDICATING, $game->gameStateMachine->currentState());

    }

    private function assertEventsContain(Game $game, string $eventName): void
    {
        $result = ArrayCollection::build(...$game->inspectEvents())->findBy(fn ($event) => $event::class === $eventName);
        $this->assertTrue($result->isDefined(), "Event $eventName not found in game events.");
    }

    private function assertEventsDoNotContain(Game $game, string $eventName): void
    {
        $result = ArrayCollection::build(...$game->inspectEvents())->findBy(fn ($event) => $event::class === $eventName);
        $this->assertTrue($result->isEmpty(), "Event $eventName found in game events.");
    }
}
