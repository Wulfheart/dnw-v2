<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Phase;

use Dnw\Game\Core\Domain\Game\Repository\Phase\AbstractPhaseRepositoryTestCase;
use Dnw\Game\Core\Domain\Game\Repository\Phase\PhaseRepositoryInterface;
use Dnw\Game\Core\Domain\Game\Repository\Phase\PhaseRepositoryLoadResult;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LaravelPhaseRepository::class)]
class LaravelPhaseRepositoryTest extends AbstractPhaseRepositoryTestCase
{
    protected function buildRepository(): PhaseRepositoryInterface
    {
        Storage::fake('local');
        $fake = Storage::disk('local');

        /** @noinspection PhpParamsInspection */
        return new LaravelPhaseRepository(
            // @phpstan-ignore-next-line
            Storage::fake()
        );
    }

    public function test_loadLinkToSvgWithOrders_returns_url(): void
    {
        $repo = $this->buildRepository();

        $phaseId = PhaseId::new();
        $repo->saveSvgWithOrders($phaseId, '::SVG::');

        $result = $repo->loadLinkToSvgWithOrders($phaseId);

        $this->assertEquals("/storage/{$phaseId}/svg_with_orders.svg", $result->unwrap());
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

        $this->assertEquals("/storage/{$phaseId}/adjudicated_svg.svg", $result->unwrap());
    }

    public function test_loadLinkToAdjudicatedSvg_returns_not_found_error(): void
    {
        $repo = $this->buildRepository();

        $phaseId = PhaseId::new();

        $result = $repo->loadLinkToAdjudicatedSvg($phaseId);

        $this->assertEquals(PhaseRepositoryLoadResult::E_NOT_FOUND, $result->unwrapErr());
    }
}
