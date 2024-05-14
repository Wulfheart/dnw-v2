<?php

namespace Dnw\Game\Tests\Unit\Domain\Entity;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Collection\PhasePowerCollection;
use Dnw\Game\Core\Domain\Collection\WinnerCollection;
use Dnw\Game\Core\Domain\Entity\Phase;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhaseId;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Phase::class)]
class PhaseTest extends TestCase
{
    public function test_hasWinners(): void
    {
        $winnerCollection = new WinnerCollection([PowerId::generate()]);
        $phase = $this->buildPhase(winnerCollection: Some::Create($winnerCollection));
        $this->assertTrue($phase->hasWinners());

        $phase = $this->buildPhase(winnerCollection: None::create());
        $this->assertFalse($phase->hasWinners());
    }

    public function test_needsOrders(): void
    {

    }

    public function test_ordersMarkedAsReady(): void
    {

    }

    public function test_markOrderStatus(): void
    {

    }

    public function test_allOrdersMarkedAsReady(): void
    {

    }

    public function test_adjudicationTimeExpired(): void
    {

    }

    public function test_applyOrders(): void
    {

    }

    /**
     * @param  ?Option<CarbonImmutable>  $adjudicationTime
     * @param  ?Option<WinnerCollection>  $winnerCollection
     */
    private function buildPhase(
        ?PhaseTypeEnum $phaseType = null,
        ?PhasePowerCollection $phasePowerCollection = null,
        ?Option $adjudicationTime = null,
        ?Option $winnerCollection = null
    ): Phase {
        return new Phase(
            PhaseId::generate(),
            $phaseType ?? PhaseTypeEnum::MOVEMENT,
            $phasePowerCollection ?? new PhasePowerCollection(),
            $adjudicationTime ?? None::create(),
            $winnerCollection ?? None::create()
        );
    }
}
