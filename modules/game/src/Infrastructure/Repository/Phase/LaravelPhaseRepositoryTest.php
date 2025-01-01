<?php

namespace Dnw\Game\Infrastructure\Repository\Phase;

use Dnw\Game\Domain\Game\Repository\Phase\AbstractPhaseRepositoryTestCase;
use Dnw\Game\Domain\Game\Repository\Phase\PhaseRepositoryInterface;
use Dnw\Game\Domain\Game\Repository\Phase\PhaseRepositoryLoadResult;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseId;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LaravelPhaseRepository::class)]
class LaravelPhaseRepositoryTest extends AbstractPhaseRepositoryTestCase
{
    protected function buildRepository(): PhaseRepositoryInterface
    {
        Storage::fake('local');
        $fake = Storage::disk('local');

        return new LaravelPhaseRepository(
            Storage::fake('local')
        );
    }

    public function test_loadLinkToSvgWithOrders_returns_url(): void
    {
        $repo = $this->buildRepository();

        $phaseId = PhaseId::new();
        $repo->saveSvgWithOrders($phaseId, '::SVG::');

        $result = $repo->loadLinkToSvgWithOrders($phaseId);

        $this->markTestSkipped();
        // $this->assertEquals("/storage/{$phaseId}/svg_with_orders.svg", $result->unwrap());
    }

    public function test_loadLinkToSvgWithOrders_returns_not_found_error(): void
    {
        $repo = $this->buildRepository();

        $phaseId = PhaseId::new();

        $result = $repo->loadLinkToAdjudicatedSvg($phaseId);

        $this->assertEquals(PhaseRepositoryLoadResult::E_NOT_FOUND, $result->unwrapErr());
    }

    public function test_loadLinkToAdjudicatedSvg_returns_url(): void
    {
        $repo = $this->buildRepository();

        $phaseId = PhaseId::new();
        $repo->saveAdjudicatedSvg($phaseId, '::SVG::');

        $result = $repo->loadLinkToAdjudicatedSvg($phaseId);

        $this->markTestSkipped();
        // $this->assertEquals("/storage/{$phaseId}/adjudicated_svg.svg", $result->unwrap());
    }

    public function test_loadLinkToAdjudicatedSvg_returns_not_found_error(): void
    {
        $repo = $this->buildRepository();

        $phaseId = PhaseId::new();

        $result = $repo->loadLinkToAdjudicatedSvg($phaseId);

        $this->assertEquals(PhaseRepositoryLoadResult::E_NOT_FOUND, $result->unwrapErr());
    }
}
