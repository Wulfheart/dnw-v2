<?php

namespace Dnw\Game\Tests\Unit\Domain\Game\Collection;

use Dnw\Game\Core\Domain\Game\Collection\PowerCollection;
use Dnw\Game\Core\Domain\Game\Collection\VariantPowerCollection;
use Dnw\Game\Core\Domain\Game\Entity\VariantPower;
use Dnw\Game\Core\Domain\Game\ValueObject\Color;
use Dnw\Game\Core\Domain\Game\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantPower\VariantPowerApiName;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantPower\VariantPowerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantPower\VariantPowerName;
use Dnw\Game\Tests\Mother\PowerMother;
use DomainException;
use PhpOption\None;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PowerCollection::class)]
class PowerCollectionTest extends TestCase
{
    public function test_createFromVariantPowerCollection(): void
    {
        $variantPowerCollection = new VariantPowerCollection(
            [
                new VariantPower(
                    VariantPowerId::generate(),
                    VariantPowerName::fromString('power1'),
                    VariantPowerApiName::fromString('power1'),
                    Color::fromString('red')
                ),
                new VariantPower(
                    VariantPowerId::generate(),
                    VariantPowerName::fromString('power2'),
                    VariantPowerApiName::fromString('power2'),
                    Color::fromString('blue')
                ),
            ]
        );

        $powerCollection = PowerCollection::createFromVariantPowerCollection($variantPowerCollection);
        for ($i = 0; $i < 2; $i++) {
            $power = $powerCollection->getOffset($i);
            $variantPower = $variantPowerCollection->getOffset($i);

            $this->assertEquals($power->variantPowerId, $variantPower->id);
            $this->assertEquals($power->playerId, None::create());
            $this->assertFalse($power->isDefeated);
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

    public function test_assign(): void
    {
        $unassignedPower = PowerMother::unassigned();
        $assignedPower = PowerMother::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $playerId = PlayerId::generate();

        $powerCollection->assign($playerId, $unassignedPower->variantPowerId);

        $this->assertEquals($playerId, $unassignedPower->playerId->get());
    }

    public function test_assign_fails_if_power_is_already_assigned_to_player(): void
    {
        $unassignedPower = PowerMother::unassigned();
        $assignedPower = PowerMother::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->expectException(DomainException::class);
        $powerCollection->assign(PlayerId::generate(), $assignedPower->variantPowerId);
    }

    public function test_hasAvailablePowers(): void
    {
        $unassignedPower = PowerMother::unassigned();
        $assignedPower = PowerMother::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertTrue($powerCollection->hasAvailablePowers());
    }

    public function test_unassign(): void
    {
        $unassignedPower = PowerMother::unassigned();
        $assignedPower = PowerMother::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $playerId = $assignedPower->playerId->get();
        $powerCollection->unassign($playerId);

        $this->assertTrue($assignedPower->playerId->isEmpty());
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

    public function test_getPowerIdByPlayerId(): void
    {
        $unassignedPower = PowerMother::unassigned();
        $assignedPower = PowerMother::assigned();

        $powerCollection = new PowerCollection([$unassignedPower, $assignedPower]);

        $this->assertEquals($assignedPower->powerId, $powerCollection->getByPlayerId($assignedPower->playerId->get()));
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
}
