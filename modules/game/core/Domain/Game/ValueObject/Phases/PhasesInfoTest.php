<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\Phases;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Core\Domain\Game\Test\Factory\PhaseFactory;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Wulfheart\Option\Option;

#[CoversClass(PhasesInfo::class)]
class PhasesInfoTest extends TestCase
{
    public function test_initialize(): void
    {
        $phasesInfo = PhasesInfo::initialize();
        $this->assertEquals(0, $phasesInfo->count->int());
        $this->assertTrue($phasesInfo->currentPhase->isNone());
        $this->assertTrue($phasesInfo->lastPhaseId->isNone());
    }

    public function test_initialPhaseExists(): void
    {
        $phasesInfo = PhasesInfo::initialize();
        $this->assertFalse($phasesInfo->initialPhaseExists());

        $phase = PhaseFactory::build();
        $phasesInfo = new PhasesInfo(Count::fromInt(1), Option::some($phase), Option::none());
        $this->assertTrue($phasesInfo->initialPhaseExists());
    }

    public function test_hasBeenStarted(): void
    {
        $phasesInfo = PhasesInfo::initialize();
        $this->assertFalse($phasesInfo->hasBeenStarted());

        $phase = PhaseFactory::build(adjudicationTime: new DateTime());
        $phasesInfo = new PhasesInfo(Count::fromInt(1), Option::some($phase), Option::none());
        $this->assertTrue($phasesInfo->hasBeenStarted());
    }

    public function test_proceedToNewPhase(): void
    {
        $phase = PhaseFactory::build(adjudicationTime: new DateTime());
        $phasesInfo = new PhasesInfo(Count::fromInt(3), Option::some($phase), Option::none());
        $newPhase = PhaseFactory::build();
        $phasesInfo->proceedToNewPhase($newPhase);

        $this->assertEquals(4, $phasesInfo->count->int());
        $this->assertEquals($newPhase, $phasesInfo->currentPhase->unwrap());
        $this->assertEquals($phase->phaseId, $phasesInfo->lastPhaseId->unwrap());
    }

    public function test_setInitialPhase(): void
    {
        $phasesInfo = PhasesInfo::initialize();
        $phase = PhaseFactory::build();
        $phasesInfo->setInitialPhase($phase);

        $this->assertEquals(1, $phasesInfo->count->int());
        $this->assertEquals($phase, $phasesInfo->currentPhase->unwrap());
    }
}
