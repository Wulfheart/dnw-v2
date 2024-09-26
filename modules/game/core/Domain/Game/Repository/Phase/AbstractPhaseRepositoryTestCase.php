<?php

namespace Dnw\Game\Core\Domain\Game\Repository\Phase;

use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use Dnw\Game\Core\Domain\Game\Exception\AlreadyPresentException;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;
use Wulfeart\Option\ResultAsserter;
use Tests\TestCase;

#[AllowLaravelTestCase]
abstract class AbstractPhaseRepositoryTestCase extends TestCase
{
    abstract protected function buildRepository(): PhaseRepositoryInterface;

    public function test_can_save_and_load_encoded_state(): void
    {
        $repository = $this->buildRepository();

        $phaseId = PhaseId::new();
        $encodedState = '::STATE::';

        $repository->saveEncodedState($phaseId, $encodedState);
        $loadedEncodedState = $repository->loadEncodedState($phaseId);

        ResultAsserter::assertOk($loadedEncodedState);

        $this->assertSame($encodedState, $loadedEncodedState->unwrap());
    }

    public function test_cannot_save_encoded_state_twice(): void
    {
        $repository = $this->buildRepository();

        $phaseId = PhaseId::new();
        $encodedState = '::STATE::';

        $repository->saveEncodedState($phaseId, $encodedState);

        $this->expectException(AlreadyPresentException::class);
        $repository->saveEncodedState($phaseId, $encodedState);
    }

    public function test_errors_if_cannot_load_encoded_state(): void
    {
        $repository = $this->buildRepository();

        $result = $repository->loadEncodedState(PhaseId::new());
        ResultAsserter::assertErr($result);
        $this->assertSame(PhaseRepositoryLoadResult::E_NOT_FOUND, $result->unwrapErr());
    }

    public function test_can_save_and_load_svg_with_orders(): void
    {
        $repository = $this->buildRepository();

        $phaseId = PhaseId::new();
        $svg = '::SVG::';

        $repository->saveSvgWithOrders($phaseId, $svg);
        $loadedSvg = $repository->loadSvgWithOrders($phaseId);

        $this->assertSame($svg, $loadedSvg->unwrap());
    }

    public function test_cannot_save_svg_with_orders_twice(): void
    {
        $repository = $this->buildRepository();

        $phaseId = PhaseId::new();
        $svg = '::SVG::';

        $repository->saveSvgWithOrders($phaseId, $svg);

        $this->expectException(AlreadyPresentException::class);
        $repository->saveSvgWithOrders($phaseId, $svg);
    }

    public function test_errors_if_cannot_load_svg_with_orders(): void
    {
        $repository = $this->buildRepository();

        $result = $repository->loadSvgWithOrders(PhaseId::new());
        $this->assertEquals(PhaseRepositoryLoadResult::E_NOT_FOUND, $result->unwrapErr());
    }

    public function test_can_save_and_load_adjudicated_svg(): void
    {
        $repository = $this->buildRepository();

        $phaseId = PhaseId::new();
        $svg = '::SVG::';

        $repository->saveAdjudicatedSvg($phaseId, $svg);
        $loadedSvg = $repository->loadAdjudicatedSvg($phaseId);

        $this->assertSame($svg, $loadedSvg->unwrap());
    }

    public function test_cannot_save_adjudicated_svg_twice(): void
    {
        $repository = $this->buildRepository();

        $phaseId = PhaseId::new();
        $svg = '::SVG::';

        $repository->saveAdjudicatedSvg($phaseId, $svg);

        $this->expectException(AlreadyPresentException::class);
        $repository->saveAdjudicatedSvg($phaseId, $svg);
    }

    public function test_errors_if_cannot_load_adjudicated_svg(): void
    {
        $repository = $this->buildRepository();

        $result = $repository->loadAdjudicatedSvg(PhaseId::new());
        $this->assertEquals(PhaseRepositoryLoadResult::E_NOT_FOUND, $result->unwrapErr());
    }
}
