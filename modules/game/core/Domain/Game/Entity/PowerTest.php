<?php

namespace Dnw\Game\Core\Domain\Game\Entity;

use Dnw\Foundation\Exception\DomainException;
use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\Test\Factory\PhasePowerDataFactory;
use Dnw\Game\Core\Domain\Game\Test\Factory\PowerFactory;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Player\ValueObject\PlayerId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Power::class)]
class PowerTest extends TestCase
{
    public function test_assign_throws_exception_if_already_assigned(): void
    {
        $playerId = PlayerId::new();

        $power = PowerFactory::build(playerId: $playerId);

        $this->expectException(DomainException::class);
        $power->assign(PlayerId::new());
    }

    public function test_assign(): void
    {
        $playerId = PlayerId::new();
        $power = PowerFactory::unassigned();

        $power->assign($playerId);

        $this->assertEquals($playerId, $power->playerId->unwrap());
    }

    public function test_unassign_throws_exception_if_not_assigned(): void
    {
        $power = PowerFactory::unassigned();

        $this->expectException(DomainException::class);
        $power->unassign();
    }

    public function test_unassign(): void
    {
        $playerId = PlayerId::new();
        $power = PowerFactory::build(playerId: $playerId);

        $power->unassign();

        $this->assertTrue($power->playerId->isNone());
    }

    public function test_markOrderStatus_throws_exception_if_no_current_phase_data(): void
    {
        $power = PowerFactory::unassigned();

        $this->expectException(DomainException::class);
        $power->markOrderStatus(true);
    }

    public function test_markOrderStatus(): void
    {
        $power = PowerFactory::build(
            currentPhaseData: PhasePowerDataFactory::build(
                markedAsReady: false,
            )
        );

        $power->markOrderStatus(true);

        $this->assertTrue($power->currentPhaseData->unwrap()->markedAsReady);
    }

    public function test_submitOrders(): void
    {
        $orderCollection = new OrderCollection();
        $power = PowerFactory::build(
            currentPhaseData: PhasePowerDataFactory::build(
                ordersNeeded: true,
            )
        );

        $power->submitOrders($orderCollection, true);

        $this->assertEquals($orderCollection, $power->currentPhaseData->unwrap()->orderCollection->unwrap());
        $this->assertTrue($power->currentPhaseData->unwrap()->markedAsReady);
    }

    public function test_submitOrders_throws_exception_if_readyForAdjudication(): void
    {
        $orderCollection = new OrderCollection();
        $power = PowerFactory::build(
            currentPhaseData: PhasePowerDataFactory::build(
                ordersNeeded: false,
            )
        );

        $this->expectException(DomainException::class);
        $power->submitOrders($orderCollection, true);

    }

    public function test_ordersNeeded(): void
    {
        $power = PowerFactory::build(
            currentPhaseData: PhasePowerDataFactory::build(
                ordersNeeded: true,
            )
        );

        $this->assertTrue($power->ordersNeeded());

        $power = PowerFactory::build(
            currentPhaseData: PhasePowerDataFactory::build(
                ordersNeeded: false,
            )
        );
        $this->assertFalse($power->ordersNeeded());

        $power = PowerFactory::build();
        $this->assertFalse($power->ordersNeeded());
    }

    public function test_ordersMarkedAsReady(): void
    {
        $power = PowerFactory::build(
            currentPhaseData: PhasePowerDataFactory::build(
                markedAsReady: true,
            )
        );

        $this->assertTrue($power->ordersMarkedAsReady());

        $power = PowerFactory::build(
            currentPhaseData: PhasePowerDataFactory::build(
                markedAsReady: false,
            )
        );
        $this->assertFalse($power->ordersMarkedAsReady());

        $power = PowerFactory::build();
        $this->assertFalse($power->ordersMarkedAsReady());
    }

    public function test_readyForAdjudication(): void
    {
        $power = PowerFactory::build(
            currentPhaseData: PhasePowerDataFactory::build(
                ordersNeeded: true,
                markedAsReady: true
            )
        );
        $this->assertTrue($power->readyForAdjudication());

        $power = PowerFactory::build(
            currentPhaseData: PhasePowerDataFactory::build(
                ordersNeeded: true,
                markedAsReady: false
            )
        );
        $this->assertFalse($power->readyForAdjudication());

        $power = PowerFactory::build(
            currentPhaseData: PhasePowerDataFactory::build(
                ordersNeeded: false,
                markedAsReady: true
            )
        );
        $this->assertTrue($power->readyForAdjudication());

        $power = PowerFactory::build(
            currentPhaseData: PhasePowerDataFactory::build(
                ordersNeeded: false,
                markedAsReady: false
            )
        );
        $this->assertTrue($power->readyForAdjudication());
    }

    public function test_proceedToNextPhase(): void
    {
        $newPhaseData = PhasePowerDataFactory::build();
        $appliedOrders = new OrderCollection();
        $power = PowerFactory::build();

        $power->proceedToNextPhase($newPhaseData, $appliedOrders);

        $this->assertEquals($newPhaseData, $power->currentPhaseData->unwrap());
        $this->assertEquals($appliedOrders, $power->appliedOrders->unwrap());
    }

    public function test_persistInitialPhase(): void
    {
        $initialPhaseData = PhasePowerDataFactory::build();
        $power = PowerFactory::build();

        $power->persistInitialPhase($initialPhaseData);

        $this->assertEquals($initialPhaseData, $power->currentPhaseData->unwrap());
    }

    public function test_isDefeated_returns_true_if_has_0_supply_centers_and_0_units(): void
    {
        $power = PowerFactory::build(
            currentPhaseData: PhasePowerDataFactory::build(
                supplyCenterCount: Count::fromInt(0),
                unitCount: Count::fromInt(0),
            )
        );

        $this->assertTrue($power->isDefeated());
    }

    public function test_isDefeated_returns_false_if_no_initial_data_is_there(): void
    {
        $power = PowerFactory::build();

        $this->assertFalse($power->isDefeated());
    }

    public function test_isDefeated_returns_true_if_has_more_than_0_supply_centers(): void
    {
        $power = PowerFactory::build(
            currentPhaseData: PhasePowerDataFactory::build(
                supplyCenterCount: Count::fromInt(1),
                unitCount: Count::fromInt(0),
            )
        );

        $this->assertFalse($power->isDefeated());
    }

    public function test_isDefeated_returns_true_if_has_more_than_0_units(): void
    {
        $power = PowerFactory::build(
            currentPhaseData: PhasePowerDataFactory::build(
                supplyCenterCount: Count::fromInt(0),
                unitCount: Count::fromInt(1),
            )
        );

        $this->assertFalse($power->isDefeated());
    }
}
