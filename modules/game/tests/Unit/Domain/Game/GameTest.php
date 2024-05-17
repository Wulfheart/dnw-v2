<?php

namespace Dnw\Game\Tests\Unit\Domain\Game;

use Carbon\CarbonImmutable;
use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\Exception\DomainException;
use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Core\Domain\Game\Dto\AdjudicationPowerDataDto;
use Dnw\Game\Core\Domain\Game\Dto\InitialAdjudicationPowerDataDto;
use Dnw\Game\Core\Domain\Game\Entity\Power;
use Dnw\Game\Core\Domain\Game\Event\GameAbandonedEvent;
use Dnw\Game\Core\Domain\Game\Event\GameAdjudicatedEvent;
use Dnw\Game\Core\Domain\Game\Event\GameCreatedEvent;
use Dnw\Game\Core\Domain\Game\Event\GameFinishedEvent;
use Dnw\Game\Core\Domain\Game\Event\GameInitializedEvent;
use Dnw\Game\Core\Domain\Game\Event\GameReadyForAdjudicationEvent;
use Dnw\Game\Core\Domain\Game\Event\GameStartedEvent;
use Dnw\Game\Core\Domain\Game\Event\OrdersSubmittedEvent;
use Dnw\Game\Core\Domain\Game\Event\PhaseMarkedAsNotReadyEvent;
use Dnw\Game\Core\Domain\Game\Event\PhaseMarkedAsReadyEvent;
use Dnw\Game\Core\Domain\Game\Event\PlayerJoinedEvent;
use Dnw\Game\Core\Domain\Game\Event\PlayerLeftEvent;
use Dnw\Game\Core\Domain\Game\Event\PowerDefeatedEvent;
use Dnw\Game\Core\Domain\Game\Game;
use Dnw\Game\Core\Domain\Game\Rule\GameRules;
use Dnw\Game\Core\Domain\Game\StateMachine\GameStates;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameName;
use Dnw\Game\Core\Domain\Game\ValueObject\Order\Order;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\NewPhaseData;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
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

        GameAsserter::assertThat($game)
            ->hasState(GameStates::ORDER_SUBMISSION)
            ->hasEvent(OrdersSubmittedEvent::class)
            ->hasNotEvent(GameReadyForAdjudicationEvent::class);
    }

    public function test_submitOrders_adjudicates_game_if_ready(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->markAllButOnePowerAsReady()->build();
        $powerToTest = $game->powerCollection->findBy(fn (Power $power) => ! $power->ordersMarkedAsReady())->get();

        $orders = OrderCollection::build(Order::fromString('A PAR - MAR'));

        $game->submitOrders($powerToTest->playerId->get(), $orders, true, new CarbonImmutable());

        $this->assertEquals($orders, $powerToTest->currentPhaseData->get()->orderCollection->get());

        GameAsserter::assertThat($game)
            ->hasState(GameStates::ADJUDICATING)
            ->hasEvent(OrdersSubmittedEvent::class)
            ->hasEvent(GameReadyForAdjudicationEvent::class);
    }

    public function test_canMarkOrderStatus_player_not_in_game(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $ruleset = $game->canMarkOrderStatus(
            PlayerId::new(),
            new CarbonImmutable(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::PLAYER_NOT_IN_GAME));
    }

    public function test_canMarkOrderStatus_player_does_not_need_to_submit_orders(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();

        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->get();
        $powerToTest->currentPhaseData->get()->ordersNeeded = false;

        $ruleset = $game->canMarkOrderStatus(
            $powerToTest->playerId->get(),
            new CarbonImmutable(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::POWER_DOES_NOT_NEED_TO_SUBMIT_ORDERS));
    }

    public function test_canMarkOrderStatus_wrong_state(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $ruleset = $game->canMarkOrderStatus(
            $game->powerCollection->findBy(fn ($power) => $power->playerId->isDefined())->get()->playerId->get(),
            new CarbonImmutable(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::EXPECTS_STATE_ORDER_SUBMISSION));
    }

    public function test_markOrderStatus_throws_exception_if_cannot_perform_action(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();

        $this->expectException(DomainException::class);
        $game->markOrderStatus(PlayerId::new(), true, new CarbonImmutable());
    }

    public function test_markOrderStatus_throws_exception_if_status_has_not_been_changed(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();
        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->get();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("Order status for power $powerToTest->powerId has not changed for game {$game->gameId}");
        $game->markOrderStatus($powerToTest->playerId->get(), false, new CarbonImmutable());
    }

    public function test_markOrderStatus_marks_orders_as_ready(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();
        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->get();

        $game->markOrderStatus($powerToTest->playerId->get(), true, new CarbonImmutable());

        $this->assertTrue($powerToTest->ordersMarkedAsReady());

        GameAsserter::assertThat($game)
            ->hasEvent(PhaseMarkedAsReadyEvent::class)
            ->hasNotEvent(GameReadyForAdjudicationEvent::class)
            ->hasState(GameStates::ORDER_SUBMISSION);
    }

    public function test_markOrderStatus_marks_orders_as_not_ready(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();
        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->get();
        $powerToTest->currentPhaseData->get()->markedAsReady = true;

        $game->markOrderStatus($powerToTest->playerId->get(), false, new CarbonImmutable());

        $this->assertFalse($powerToTest->ordersMarkedAsReady());

        GameAsserter::assertThat($game)
            ->hasEvent(PhaseMarkedAsNotReadyEvent::class)
            ->hasNotEvent(GameReadyForAdjudicationEvent::class)
            ->hasState(GameStates::ORDER_SUBMISSION);
    }

    public function test_markOrderStatus_transitions_state_to_adjudicating_if_is_ready(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->markAllButOnePowerAsReady()->build();
        $powerToTest = $game->powerCollection->findBy(fn ($power) => ! $power->ordersMarkedAsReady())->get();

        $game->markOrderStatus($powerToTest->playerId->get(), true, new CarbonImmutable());

        $this->assertTrue($powerToTest->ordersMarkedAsReady());

        GameAsserter::assertThat($game)
            ->hasEvent(PhaseMarkedAsReadyEvent::class)
            ->hasEvent(GameReadyForAdjudicationEvent::class)
            ->hasState(GameStates::ADJUDICATING);
    }

    public function test_canAdjudicate_expects_state(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $ruleset = $game->canAdjudicate(new CarbonImmutable());

        $this->assertTrue($ruleset->containsViolation(GameRules::EXPECTS_STATE_ADJUDICATING));
    }

    public function test_applyAdjudication_throws_exception_if_it_is_not_permitted(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();

        $this->expectException(DomainException::class);
        $game->applyAdjudication(PhaseTypeEnum::MOVEMENT, new ArrayCollection(), new CarbonImmutable());
    }

    public function test_applyAdjudication_without_winners(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->transitionToAdjudicating()->build();

        $game->powerCollection->map(fn (Power $power) => new AdjudicationPowerDataDto(
            $power->powerId,
            new NewPhaseData(true, false, Count::fromInt(1), Count::fromInt(1)),
            new OrderCollection()
        ));
        $powerWhichIsAlreadyDefeated = $game->powerCollection->first();
        $defeatedPhaseData = $powerWhichIsAlreadyDefeated->currentPhaseData->get();
        $defeatedPhaseData->ordersNeeded = false;
        $defeatedPhaseData->supplyCenterCount = Count::fromInt(0);
        $defeatedPhaseData->unitCount = Count::fromInt(0);

        $powerWhichWillBeDefeated = $game->powerCollection->getOffset(1);

        $adjudicationPowerDataCollection = $game->powerCollection->map(fn (Power $power) => new AdjudicationPowerDataDto(
            $power->powerId,
            new NewPhaseData(true, false, Count::fromInt(1), Count::fromInt(1)),
            new OrderCollection()
        ));

        /** @var AdjudicationPowerDataDto $defeatedPhasePowerData */
        $defeatedPhasePowerData = $adjudicationPowerDataCollection->findBy(fn (AdjudicationPowerDataDto $adjudicationPowerData) => $adjudicationPowerData->powerId === $powerWhichIsAlreadyDefeated->powerId)->get();
        $defeatedPhasePowerData->newPhaseData->supplyCenterCount = Count::fromInt(0);
        $defeatedPhasePowerData->newPhaseData->unitCount = Count::fromInt(0);
        $defeatedPhasePowerData->newPhaseData->ordersNeeded = false;

        $powerWhichWillBeDefeatedPhasePowerData = $adjudicationPowerDataCollection->findBy(fn (AdjudicationPowerDataDto $adjudicationPowerData) => $adjudicationPowerData->powerId === $powerWhichWillBeDefeated->powerId)->get();
        $powerWhichWillBeDefeatedPhasePowerData->newPhaseData->supplyCenterCount = Count::fromInt(0);
        $powerWhichWillBeDefeatedPhasePowerData->newPhaseData->unitCount = Count::fromInt(0);
        $powerWhichWillBeDefeatedPhasePowerData->newPhaseData->ordersNeeded = false;

        $game->applyAdjudication(PhaseTypeEnum::MOVEMENT, $adjudicationPowerDataCollection, new CarbonImmutable());

        GameAsserter::assertThat($game)
            ->hasEvent(GameAdjudicatedEvent::class)
            ->hasEvent(PowerDefeatedEvent::class)
            ->hasEvent(fn (PowerDefeatedEvent $event) => $event->powerId->equals($powerWhichWillBeDefeated->powerId->toId()))
            ->hasEvent(PowerDefeatedEvent::class, 1)
            ->hasState(GameStates::ORDER_SUBMISSION);
    }

    public function test_applyAdjudication_with_winners(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->transitionToAdjudicating()->build();

        $game->powerCollection->map(fn (Power $power) => new AdjudicationPowerDataDto(
            $power->powerId,
            new NewPhaseData(true, false, Count::fromInt(1), Count::fromInt(1)),
            new OrderCollection()
        ));
        $powerWhichWillWin = $game->powerCollection->first();

        $adjudicationPowerDataCollection = $game->powerCollection->map(fn (Power $power) => new AdjudicationPowerDataDto(
            $power->powerId,
            new NewPhaseData(true, false, Count::fromInt(1), Count::fromInt(1)),
            new OrderCollection()
        ));

        /** @var AdjudicationPowerDataDto $defeatedPhasePowerData */
        $defeatedPhasePowerData = $adjudicationPowerDataCollection->findBy(fn (AdjudicationPowerDataDto $adjudicationPowerData) => $adjudicationPowerData->powerId === $powerWhichWillWin->powerId)->get();
        $defeatedPhasePowerData->newPhaseData->isWinner = true;

        $game->applyAdjudication(PhaseTypeEnum::MOVEMENT, $adjudicationPowerDataCollection, new CarbonImmutable());

        GameAsserter::assertThat($game)
            ->hasEvent(GameAdjudicatedEvent::class)
            ->hasEvent(GameFinishedEvent::class)
            ->hasState(GameStates::FINISHED);
    }

    public function test_canApplyInitialAdjudication_needs_correct_state(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $ruleset = $game->canApplyInitialAdjudication();

        $this->assertTrue($ruleset->containsViolation(GameRules::EXPECTS_STATE_CREATED));
    }

    public function test_applyInitialAdjudication_throws_exception_if_not_permitted(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();

        $this->expectException(DomainException::class);
        $game->applyInitialAdjudication(PhaseTypeEnum::MOVEMENT, new ArrayCollection(), new CarbonImmutable());
    }

    public function test_applyInitialAdjudication_happy_path(): void
    {
        $game = GameBuilder::initialize()->build();

        $initialAdjudicationPowerData = $game->powerCollection->map(fn (Power $power) => new InitialAdjudicationPowerDataDto(
            $power->powerId,
            new NewPhaseData(true, false, Count::fromInt(1), Count::fromInt(1)),
        ));

        $game->applyInitialAdjudication(PhaseTypeEnum::MOVEMENT, $initialAdjudicationPowerData, new CarbonImmutable());

        GameAsserter::assertThat($game)
            ->hasState(GameStates::PLAYERS_JOINING)
            ->hasEvent(GameInitializedEvent::class);
    }
}
