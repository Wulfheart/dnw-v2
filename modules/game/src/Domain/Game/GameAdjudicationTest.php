<?php

namespace Dnw\Game\Domain\Game;

use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\DateTime\DateTime;
use Dnw\Foundation\Exception\DomainException;
use Dnw\Game\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Domain\Game\Dto\AdjudicationPowerDataDto;
use Dnw\Game\Domain\Game\Dto\InitialAdjudicationPowerDataDto;
use Dnw\Game\Domain\Game\Entity\Power;
use Dnw\Game\Domain\Game\Event\GameAdjudicatedEvent;
use Dnw\Game\Domain\Game\Event\GameFinishedEvent;
use Dnw\Game\Domain\Game\Event\GameInitializedEvent;
use Dnw\Game\Domain\Game\Event\GameReadyForAdjudicationEvent;
use Dnw\Game\Domain\Game\Event\OrdersSubmittedEvent;
use Dnw\Game\Domain\Game\Event\PhaseMarkedAsNotReadyEvent;
use Dnw\Game\Domain\Game\Event\PhaseMarkedAsReadyEvent;
use Dnw\Game\Domain\Game\Event\PowerDefeatedEvent;
use Dnw\Game\Domain\Game\Rule\GameRules;
use Dnw\Game\Domain\Game\StateMachine\GameStates;
use Dnw\Game\Domain\Game\Test\Asserter\GameAsserter;
use Dnw\Game\Domain\Game\Test\Factory\GameBuilder;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Game\ValueObject\Order\Order;
use Dnw\Game\Domain\Game\ValueObject\Phase\NewPhaseData;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Game::class)]
class GameAdjudicationTest extends TestCase
{
    public function test_calculate_supply_center_count_for_winning(): void
    {
        $game = GameBuilder::initialize()->build();

        $this->assertEquals(18, $game->calculateSupplyCenterCountForWinning()->int());
    }

    public function test_can_submit_orders_unexpected_status(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $ruleset = $game->canSubmitOrders(
            $game->powerCollection->findBy(fn ($power) => $power->playerId->isSome())->unwrap()->playerId->unwrap(),
            new DateTime(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::EXPECTS_STATE_ORDER_SUBMISSION));
    }

    public function test_can_submit_orders_player_is_not_in_game(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $ruleset = $game->canSubmitOrders(
            PlayerId::new(),
            new DateTime(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::PLAYER_NOT_IN_GAME));
    }

    public function test_can_submit_orders_player_does_not_need_to_submit_orders(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();

        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->unwrap();
        $powerToTest->currentPhaseData->unwrap()->ordersNeeded = false;

        $ruleset = $game->canSubmitOrders(
            $powerToTest->playerId->unwrap(),
            new DateTime(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::POWER_DOES_NOT_NEED_TO_SUBMIT_ORDERS));
    }

    public function test_can_submit_orders_player_has_orders_already_marked_as_ready(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->markOnePowerAsReady()->build();
        $ruleset = $game->canSubmitOrders(
            $game->powerCollection->findBy(fn ($power) => $power->ordersMarkedAsReady())->unwrap()->playerId->unwrap(),
            new DateTime(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::ORDERS_ALREADY_MARKED_AS_READY));
    }

    public function test_can_submit_orders_fails_if_adjudication_time_is_expired(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->markOnePowerAsReady()->build();
        $time = $game->phasesInfo->currentPhase->unwrap()->adjudicationTime->unwrap()->addMinute();
        $ruleset = $game->canSubmitOrders(
            $game->powerCollection->findBy(fn ($power) => $power->ordersMarkedAsReady())->unwrap()->playerId->unwrap(),
            $time,
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::GAME_PHASE_TIME_EXCEEDED));
    }

    public function test_submit_orders_throws_exception_if_orders_cannot_be_submitted(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $this->expectException(DomainException::class);
        $game->submitOrders(PlayerId::new(), new OrderCollection(), false, new DateTime());
    }

    public function test_submit_orders_throws_exception_if_the_orders_have_not_changed(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();
        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->unwrap();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("Power $powerToTest->powerId cannot submit empty orders for game $game->gameId");
        $game->submitOrders($powerToTest->playerId->unwrap(), new OrderCollection(), true, new DateTime());
    }

    public function test_submit_orders_does_not_allow_resubmission_of_the_exact_same_orders(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();
        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->unwrap();

        $orders = OrderCollection::build(Order::fromString('A PAR - MAR'));

        $game->submitOrders($powerToTest->playerId->unwrap(), $orders, false, new DateTime());

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("Power $powerToTest->powerId has already submitted orders exactly the same orders for game $game->gameId");
        $game->submitOrders($powerToTest->playerId->unwrap(), $orders, false, new DateTime());

    }

    public function test_submit_orders_does_not_adjudicate_game_if_not_ready(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();
        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->unwrap();

        $orders = OrderCollection::build(Order::fromString('A PAR - MAR'));

        $game->submitOrders($powerToTest->playerId->unwrap(), $orders, true, new DateTime());

        $this->assertEquals($orders, $powerToTest->currentPhaseData->unwrap()->orderCollection->unwrap());

        GameAsserter::assertThat($game)
            ->hasState(GameStates::ORDER_SUBMISSION)
            ->hasEvent(OrdersSubmittedEvent::class)
            ->hasNotEvent(GameReadyForAdjudicationEvent::class);
    }

    public function test_submit_orders_adjudicates_game_if_ready(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->markAllButOnePowerAsReady()->build();
        $powerToTest = $game->powerCollection->findBy(fn (Power $power) => ! $power->ordersMarkedAsReady())->unwrap();

        $orders = OrderCollection::build(Order::fromString('A PAR - MAR'));

        $game->submitOrders($powerToTest->playerId->unwrap(), $orders, true, new DateTime());

        $this->assertEquals($orders, $powerToTest->currentPhaseData->unwrap()->orderCollection->unwrap());

        GameAsserter::assertThat($game)
            ->hasState(GameStates::ADJUDICATING)
            ->hasEvent(OrdersSubmittedEvent::class)
            ->hasEvent(GameReadyForAdjudicationEvent::class);
    }

    public function test_can_mark_order_status_player_not_in_game(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $ruleset = $game->canMarkOrderStatus(
            PlayerId::new(),
            new DateTime(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::PLAYER_NOT_IN_GAME));
    }

    public function test_can_mark_order_status_player_does_not_need_to_submit_orders(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();

        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->unwrap();
        $powerToTest->currentPhaseData->unwrap()->ordersNeeded = false;

        $ruleset = $game->canMarkOrderStatus(
            $powerToTest->playerId->unwrap(),
            new DateTime(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::POWER_DOES_NOT_NEED_TO_SUBMIT_ORDERS));
    }

    public function test_can_mark_order_status_wrong_state(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $ruleset = $game->canMarkOrderStatus(
            $game->powerCollection->findBy(fn ($power) => $power->playerId->isSome())->unwrap()->playerId->unwrap(),
            new DateTime(),
        );

        $this->assertTrue($ruleset->containsViolation(GameRules::EXPECTS_STATE_ORDER_SUBMISSION));
    }

    public function test_mark_order_status_throws_exception_if_cannot_perform_action(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();

        $this->expectException(DomainException::class);
        $game->markOrderStatus(PlayerId::new(), true, new DateTime());
    }

    public function test_mark_order_status_throws_exception_if_status_has_not_been_changed(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();
        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->unwrap();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("Order status for power $powerToTest->powerId has not changed for game {$game->gameId}");
        $game->markOrderStatus($powerToTest->playerId->unwrap(), false, new DateTime());
    }

    public function test_mark_order_status_marks_orders_as_ready(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();
        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->unwrap();

        $game->markOrderStatus($powerToTest->playerId->unwrap(), true, new DateTime());

        $this->assertTrue($powerToTest->ordersMarkedAsReady());

        GameAsserter::assertThat($game)
            ->hasEvent(PhaseMarkedAsReadyEvent::class)
            ->hasNotEvent(GameReadyForAdjudicationEvent::class)
            ->hasState(GameStates::ORDER_SUBMISSION);
    }

    public function test_mark_order_status_marks_orders_as_not_ready(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();
        $powerToTest = $game->powerCollection->findBy(fn ($power) => $power->ordersNeeded())->unwrap();
        $powerToTest->currentPhaseData->unwrap()->markedAsReady = true;

        $game->markOrderStatus($powerToTest->playerId->unwrap(), false, new DateTime());

        $this->assertFalse($powerToTest->ordersMarkedAsReady());

        GameAsserter::assertThat($game)
            ->hasEvent(PhaseMarkedAsNotReadyEvent::class)
            ->hasNotEvent(GameReadyForAdjudicationEvent::class)
            ->hasState(GameStates::ORDER_SUBMISSION);
    }

    public function test_mark_order_status_transitions_state_to_adjudicating_if_is_ready(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->markAllButOnePowerAsReady()->build();
        $powerToTest = $game->powerCollection->findBy(fn ($power) => ! $power->ordersMarkedAsReady())->unwrap();

        $game->markOrderStatus($powerToTest->playerId->unwrap(), true, new DateTime());

        $this->assertTrue($powerToTest->ordersMarkedAsReady());

        GameAsserter::assertThat($game)
            ->hasEvent(PhaseMarkedAsReadyEvent::class)
            ->hasEvent(GameReadyForAdjudicationEvent::class)
            ->hasState(GameStates::ADJUDICATING);
    }

    public function test_can_adjudicate_expects_state(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $ruleset = $game->canAdjudicate(new DateTime());

        $this->assertTrue($ruleset->containsViolation(GameRules::EXPECTS_STATE_ADJUDICATING));
    }

    public function test_apply_adjudication_throws_exception_if_it_is_not_permitted(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->build();

        $this->expectException(DomainException::class);
        $game->applyAdjudication(PhaseTypeEnum::MOVEMENT, new ArrayCollection(), new DateTime());
    }

    public function test_apply_adjudication_without_winners(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->start()->transitionToAdjudicating()->build();

        $game->powerCollection->map(fn (Power $power) => new AdjudicationPowerDataDto(
            $power->powerId,
            new NewPhaseData(true, false, Count::fromInt(1), Count::fromInt(1)),
            new OrderCollection()
        ));
        $powerWhichIsAlreadyDefeated = $game->powerCollection->first();
        $defeatedPhaseData = $powerWhichIsAlreadyDefeated->currentPhaseData->unwrap();
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
        $defeatedPhasePowerData = $adjudicationPowerDataCollection->findBy(fn (AdjudicationPowerDataDto $adjudicationPowerData) => $adjudicationPowerData->powerId === $powerWhichIsAlreadyDefeated->powerId)->unwrap();
        $defeatedPhasePowerData->newPhaseData->supplyCenterCount = Count::fromInt(0);
        $defeatedPhasePowerData->newPhaseData->unitCount = Count::fromInt(0);
        $defeatedPhasePowerData->newPhaseData->ordersNeeded = false;

        $powerWhichWillBeDefeatedPhasePowerData = $adjudicationPowerDataCollection->findBy(fn (AdjudicationPowerDataDto $adjudicationPowerData) => $adjudicationPowerData->powerId === $powerWhichWillBeDefeated->powerId)->unwrap();
        $powerWhichWillBeDefeatedPhasePowerData->newPhaseData->supplyCenterCount = Count::fromInt(0);
        $powerWhichWillBeDefeatedPhasePowerData->newPhaseData->unitCount = Count::fromInt(0);
        $powerWhichWillBeDefeatedPhasePowerData->newPhaseData->ordersNeeded = false;

        $game->applyAdjudication(PhaseTypeEnum::MOVEMENT, $adjudicationPowerDataCollection, new DateTime());

        GameAsserter::assertThat($game)
            ->hasEvent(GameAdjudicatedEvent::class)
            ->hasEvent(PowerDefeatedEvent::class)
            ->hasEvent(fn (PowerDefeatedEvent $event) => $event->powerId->equals($powerWhichWillBeDefeated->powerId->toId()))
            ->hasEvent(PowerDefeatedEvent::class, 1)
            ->hasState(GameStates::ORDER_SUBMISSION);
    }

    public function test_apply_adjudication_with_winners(): void
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
        $defeatedPhasePowerData = $adjudicationPowerDataCollection->findBy(fn (AdjudicationPowerDataDto $adjudicationPowerData) => $adjudicationPowerData->powerId === $powerWhichWillWin->powerId)->unwrap();
        $defeatedPhasePowerData->newPhaseData->isWinner = true;

        $game->applyAdjudication(PhaseTypeEnum::MOVEMENT, $adjudicationPowerDataCollection, new DateTime());

        GameAsserter::assertThat($game)
            ->hasEvent(GameAdjudicatedEvent::class)
            ->hasEvent(GameFinishedEvent::class)
            ->hasState(GameStates::FINISHED);
    }

    public function test_can_apply_initial_adjudication_needs_correct_state(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();
        $ruleset = $game->canApplyInitialAdjudication();

        $this->assertTrue($ruleset->containsViolation(GameRules::EXPECTS_STATE_CREATED));
    }

    public function test_apply_initial_adjudication_throws_exception_if_not_permitted(): void
    {
        $game = GameBuilder::initialize()->storeInitialAdjudication()->build();

        $this->expectException(DomainException::class);
        $game->applyInitialAdjudication(PhaseTypeEnum::MOVEMENT, new ArrayCollection(), new DateTime());
    }

    public function test_apply_initial_adjudication_happy_path(): void
    {
        $game = GameBuilder::initialize()->build();

        $initialAdjudicationPowerData = $game->powerCollection->map(fn (Power $power) => new InitialAdjudicationPowerDataDto(
            $power->powerId,
            new NewPhaseData(true, false, Count::fromInt(1), Count::fromInt(1)),
        ));

        $game->applyInitialAdjudication(PhaseTypeEnum::MOVEMENT, $initialAdjudicationPowerData, new DateTime());

        GameAsserter::assertThat($game)
            ->hasState(GameStates::PLAYERS_JOINING)
            ->hasEvent(GameInitializedEvent::class);
    }
}
