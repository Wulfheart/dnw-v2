<?php

namespace Dnw\Game\Tests\Unit\Domain\Game\Collection;

use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\Collection\PhasePowerCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;
use PhpOption\None;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(PhasePowerCollection::class)]
class PhasePowerCollectionTest extends TestCase
{
    public function test_setOrdersForPower_happy_path(): void
    {
        $phasePowerData = new PhasePowerData(
            PowerId::generate(),
            true,
            false,
            Count::zero(),
            Count::zero(),
            None::create(),
            None::create(),
        );

        $phasePowerCollection = new PhasePowerCollection([$phasePowerData]);

        $orderCollection = OrderCollection::fromStringArray(['order1', 'order2', 'order3']);
        $phasePowerCollection->setOrdersForPower($phasePowerData->powerId, $orderCollection, true);

        $changedPhasePowerData = $phasePowerCollection->toArray()[0];
        $this->assertEquals($orderCollection, $changedPhasePowerData->orderCollection->get());
        $this->assertTrue($changedPhasePowerData->markedAsReady);
    }

    public function test_setOrdersForPower_throws_exception_if_power_not_present(): void
    {
        $phasePowerCollection = new PhasePowerCollection();

        $this->expectException(RuntimeException::class);
        $phasePowerCollection->setOrdersForPower(PowerId::generate(), OrderCollection::fromStringArray([]), true);
    }

    public function test_needsOrdersFromPower(): void
    {
        $phasePowerData = new PhasePowerData(
            PowerId::generate(),
            true,
            false,
            Count::zero(),
            Count::zero(),
            None::create(),
            None::create(),
        );

        $phasePowerCollection = new PhasePowerCollection([$phasePowerData]);

        $this->assertTrue($phasePowerCollection->needsOrdersFromPower($phasePowerData->powerId));
    }

    public function test_markOrderStatus(): void
    {
        $phasePowerData = new PhasePowerData(
            PowerId::generate(),
            true,
            false,
            Count::zero(),
            Count::zero(),
            None::create(),
            None::create(),
        );

        $phasePowerCollection = new PhasePowerCollection([$phasePowerData]);

        $phasePowerCollection->markOrderStatus($phasePowerData->powerId, true);

        $changedPhasePowerData = $phasePowerCollection->toArray()[0];
        $this->assertTrue($changedPhasePowerData->markedAsReady);
    }

    public function test_ordersMarkedAsReadyFromPower(): void
    {
        $phasePowerData = new PhasePowerData(
            PowerId::generate(),
            true,
            false,
            Count::zero(),
            Count::zero(),
            None::create(),
            None::create(),
        );

        $phasePowerCollection = new PhasePowerCollection([$phasePowerData]);

        $this->assertFalse($phasePowerCollection->ordersMarkedAsReadyFromPower($phasePowerData->powerId));
    }
}
