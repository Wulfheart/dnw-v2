<?php

namespace Dnw\Game\Tests\Unit\Domain\Game\Collection;

use Dnw\Game\Core\Domain\Game\Collection\PowerCollection;
use Dnw\Game\Core\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Tests\Mother\PowerMother;
use PhpOption\None;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PowerCollection::class)]
class PowerCollectionTest extends TestCase
{
    public function test_createFromVariantPowerCollection(): void
    {
        $variantPowerIdCollection = VariantPowerIdCollection::build(VariantPowerId::generate(), VariantPowerId::generate());

        $powerCollection = PowerCollection::createFromVariantPowerIdCollection($variantPowerIdCollection);
        for ($i = 0; $i < 2; $i++) {
            $power = $powerCollection->getOffset($i);
            $variantPowerId = $variantPowerIdCollection->getOffset($i);

            $this->assertEquals($power->variantPowerId, $variantPowerId);
            $this->assertEquals($power->playerId, None::create());
        }

    }

    public function test_getUnassignedPowers(): void
    {
        $unassignedPower = PowerMother::unassigned();
        $assignedPower = PowerMother::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $unassignedPowers = $powerCollection->getUnassignedPowers();

        $this->assertCount(1, $unassignedPowers);
        $this->assertEquals($unassignedPower, $unassignedPowers->getOffset(0));
    }

    public function test_hasAvailablePowers(): void
    {
        $unassignedPower = PowerMother::unassigned();
        $assignedPower = PowerMother::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertTrue($powerCollection->hasAvailablePowers());
    }

    public function test_containsPlayer_and_doesNotContainPlayer(): void
    {
        $unassignedPower = PowerMother::unassigned();
        $assignedPower = PowerMother::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertTrue($powerCollection->containsPlayer($assignedPower->playerId->get()));
        $this->assertFalse($powerCollection->doesNotContainPlayer($assignedPower->playerId->get()));

        $this->assertFalse($powerCollection->containsPlayer(PlayerId::generate()));
        $this->assertTrue($powerCollection->doesNotContainPlayer(PlayerId::generate()));
    }

    public function test_hasPowerFilled(): void
    {
        $unassignedPower = PowerMother::unassigned();
        $assignedPower = PowerMother::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertTrue($powerCollection->hasPowerFilled($assignedPower->variantPowerId));
        $this->assertFalse($powerCollection->hasPowerFilled($unassignedPower->variantPowerId));
    }

    public function test_hasNoAssignedPowers_returns_true(): void
    {
        $powerCollection = new PowerCollection([PowerMother::unassigned(), PowerMother::unassigned()]);

        $this->assertTrue($powerCollection->hasNoAssignedPowers());
    }

    public function test_hasNoAssignedPowers_returns_false(): void
    {
        $unassignedPower = PowerMother::unassigned();
        $assignedPower = PowerMother::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertFalse($powerCollection->hasNoAssignedPowers());
    }

    public function test_getPowerByPlayerId(): void
    {
        $unassignedPower = PowerMother::unassigned();
        $assignedPower = PowerMother::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertEquals($assignedPower, $powerCollection->getByPlayerId($assignedPower->playerId->get()));
    }

    public function test_getByPowerId(): void
    {
        $unassignedPower = PowerMother::unassigned();
        $assignedPower = PowerMother::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertEquals($assignedPower, $powerCollection->getByPowerId($assignedPower->powerId));
    }

    public function test_getByVariantPowerId(): void
    {
        $unassignedPower = PowerMother::unassigned();
        $assignedPower = PowerMother::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertEquals($assignedPower, $powerCollection->getByVariantPowerId($assignedPower->variantPowerId));
    }

    public function test_some_things(): void
    {
        $powerOne = PowerMother::unassigned();
        $powerTwo = PowerMother::unassigned();

        $powerCollection = new PowerCollection([$powerOne, $powerTwo]);

        $powerTwo->assign(PlayerId::generate());

        $two = $powerCollection->getByPowerId($powerTwo->powerId);
        $this->assertTrue($two->playerId->isDefined());

    }
}
