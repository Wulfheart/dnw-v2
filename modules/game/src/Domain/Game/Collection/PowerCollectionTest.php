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
    public function test_create_from_variant_power_collection(): void
    {
        $variantPowerIdCollection = VariantPowerIdCollection::build(VariantPowerId::new(), VariantPowerId::new());

        $powerCollection = PowerCollection::createFromVariantPowerIdCollection($variantPowerIdCollection);
        for ($i = 0; $i < 2; $i++) {
            $power = $powerCollection->getOffset($i);
            $variantPowerId = $variantPowerIdCollection->getOffset($i);

            $this->assertEquals($power->variantPowerId, $variantPowerId);
            $this->assertEquals($power->playerId, Option::none());
        }

    }

    public function test_get_unassigned_powers(): void
    {
        $unassignedPower = PowerFactory::unassigned();
        $assignedPower = PowerFactory::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $unassignedPowers = $powerCollection->getUnassignedPowers();

        $this->assertCount(1, $unassignedPowers);
        $this->assertEquals($unassignedPower, $unassignedPowers->getOffset(0));
    }

    public function test_has_available_powers(): void
    {
        $unassignedPower = PowerFactory::unassigned();
        $assignedPower = PowerFactory::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertTrue($powerCollection->hasAvailablePowers());
    }

    public function test_contains_player_and_does_not_contain_player(): void
    {
        $unassignedPower = PowerFactory::unassigned();
        $assignedPower = PowerFactory::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertTrue($powerCollection->containsPlayer($assignedPower->playerId->unwrap()));
        $this->assertFalse($powerCollection->doesNotContainPlayer($assignedPower->playerId->unwrap()));

        $this->assertFalse($powerCollection->containsPlayer(PlayerId::new()));
        $this->assertTrue($powerCollection->doesNotContainPlayer(PlayerId::new()));
    }

    public function test_has_power_filled(): void
    {
        $unassignedPower = PowerFactory::unassigned();
        $assignedPower = PowerFactory::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertTrue($powerCollection->hasPowerFilled($assignedPower->variantPowerId));
        $this->assertFalse($powerCollection->hasPowerFilled($unassignedPower->variantPowerId));
    }

    public function test_has_no_assigned_powers_returns_true(): void
    {
        $powerCollection = new PowerCollection([PowerFactory::unassigned(), PowerFactory::unassigned()]);

        $this->assertTrue($powerCollection->hasNoAssignedPowers());
    }

    public function test_has_no_assigned_powers_returns_false(): void
    {
        $unassignedPower = PowerFactory::unassigned();
        $assignedPower = PowerFactory::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertFalse($powerCollection->hasNoAssignedPowers());
    }

    public function test_get_power_by_player_id(): void
    {
        $unassignedPower = PowerFactory::unassigned();
        $assignedPower = PowerFactory::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertEquals($assignedPower, $powerCollection->getByPlayerId($assignedPower->playerId->unwrap()));
    }

    public function test_get_by_power_id(): void
    {
        $unassignedPower = PowerFactory::unassigned();
        $assignedPower = PowerFactory::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertEquals($assignedPower, $powerCollection->getByPowerId($assignedPower->powerId));
    }

    public function test_get_by_variant_power_id(): void
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
