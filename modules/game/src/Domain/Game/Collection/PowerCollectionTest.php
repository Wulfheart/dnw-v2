<?php

namespace Dnw\Game\Domain\Game\Collection;

use Dnw\Game\Domain\Game\Test\Factory\PowerFactory;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Domain\Variant\Shared\VariantPowerId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Wulfheart\Option\Option;

#[CoversClass(PowerCollection::class)]
class PowerCollectionTest extends TestCase
{
    public function test_createFromVariantPowerCollection(): void
    {
        $variantPowerIdCollection = VariantPowerIdCollection::build(
            VariantPowerId::new('First'),
            VariantPowerId::new('Second')
        );

        $powerCollection = PowerCollection::createFromVariantPowerIdCollection($variantPowerIdCollection);
        for ($i = 0; $i < 2; $i++) {
            $power = $powerCollection->getOffset($i);
            $variantPowerId = $variantPowerIdCollection->getOffset($i);

            $this->assertEquals($power->variantPowerId, $variantPowerId);
            $this->assertEquals($power->playerId, Option::none());
        }

    }

    public function test_getUnassignedPowers(): void
    {
        $unassignedPower = PowerFactory::unassigned();
        $assignedPower = PowerFactory::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $unassignedPowers = $powerCollection->getUnassignedPowers();

        $this->assertCount(1, $unassignedPowers);
        $this->assertEquals($unassignedPower, $unassignedPowers->getOffset(0));
    }

    public function test_hasAvailablePowers(): void
    {
        $unassignedPower = PowerFactory::unassigned();
        $assignedPower = PowerFactory::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertTrue($powerCollection->hasAvailablePowers());
    }

    public function test_containsPlayer_and_doesNotContainPlayer(): void
    {
        $unassignedPower = PowerFactory::unassigned();
        $assignedPower = PowerFactory::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertTrue($powerCollection->containsPlayer($assignedPower->playerId->unwrap()));
        $this->assertFalse($powerCollection->doesNotContainPlayer($assignedPower->playerId->unwrap()));

        $this->assertFalse($powerCollection->containsPlayer(PlayerId::new()));
        $this->assertTrue($powerCollection->doesNotContainPlayer(PlayerId::new()));
    }

    public function test_hasPowerFilled(): void
    {
        $unassignedPower = PowerFactory::unassigned();
        $assignedPower = PowerFactory::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertTrue($powerCollection->hasPowerFilled($assignedPower->variantPowerId));
        $this->assertFalse($powerCollection->hasPowerFilled($unassignedPower->variantPowerId));
    }

    public function test_hasNoAssignedPowers_returns_true(): void
    {
        $powerCollection = new PowerCollection([PowerFactory::unassigned(), PowerFactory::unassigned()]);

        $this->assertTrue($powerCollection->hasNoAssignedPowers());
    }

    public function test_hasNoAssignedPowers_returns_false(): void
    {
        $unassignedPower = PowerFactory::unassigned();
        $assignedPower = PowerFactory::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertFalse($powerCollection->hasNoAssignedPowers());
    }

    public function test_getPowerByPlayerId(): void
    {
        $unassignedPower = PowerFactory::unassigned();
        $assignedPower = PowerFactory::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertEquals($assignedPower, $powerCollection->getByPlayerId($assignedPower->playerId->unwrap()));
    }

    public function test_getByPowerId(): void
    {
        $unassignedPower = PowerFactory::unassigned();
        $assignedPower = PowerFactory::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertEquals($assignedPower, $powerCollection->getByPowerId($assignedPower->powerId));
    }

    public function test_getByVariantPowerId(): void
    {
        $unassignedPower = PowerFactory::unassigned();
        $assignedPower = PowerFactory::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertEquals($assignedPower, $powerCollection->getByVariantPowerId($assignedPower->variantPowerId));
    }

    public function test_some_things(): void
    {
        $powerOne = PowerFactory::unassigned();
        $powerTwo = PowerFactory::unassigned();

        $powerCollection = new PowerCollection([$powerOne, $powerTwo]);

        $powerTwo->assign(PlayerId::new());

        $two = $powerCollection->getByPowerId($powerTwo->powerId);
        $this->assertTrue($two->playerId->isSome());

    }
}
