<?php

namespace Dnw\Game\Infrastructure\Repository\Phase;

use Dnw\Game\Domain\Game\Exception\AlreadyPresentException;
use Dnw\Game\Domain\Game\Repository\Phase\PhaseRepositoryInterface;
use Dnw\Game\Domain\Game\Repository\Phase\PhaseRepositoryLoadResult;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseId;
use Illuminate\Filesystem\FilesystemAdapter;

class LaravelPhaseRepository implements PhaseRepositoryInterface
{
    private const ENCODED_STATE_PATTERN = '%s/encoded_state.json';

    private const SVG_WITH_ORDERS_PATTERN = '%s/svg_with_orders.svg';

    private const ADJUDICATED_SVG_PATTERN = '%s/adjudicated_svg.svg';

    public function __construct(
        private FilesystemAdapter $filesystem,
    ) {}

    public function loadEncodedState(PhaseId $phaseId): PhaseRepositoryLoadResult
    {
        $state = $this->filesystem->get(
            sprintf(self::ENCODED_STATE_PATTERN, (string) $phaseId)
        );
        if ($state == null) {
            return PhaseRepositoryLoadResult::err(PhaseRepositoryLoadResult::E_NOT_FOUND);
        }

        return PhaseRepositoryLoadResult::ok($state);
    }

    public function saveEncodedState(PhaseId $phaseId, string $encodedState): void
    {
        $path = sprintf(self::ENCODED_STATE_PATTERN, (string) $phaseId);
        if ($this->filesystem->exists($path)) {
            throw new AlreadyPresentException($path);
        }

        $this->filesystem->put(
            sprintf(self::ENCODED_STATE_PATTERN, (string) $phaseId),
            $encodedState
        );
    }

    public function loadSvgWithOrders(PhaseId $phaseId): PhaseRepositoryLoadResult
    {
        $data = $this->filesystem->get(
            sprintf(self::SVG_WITH_ORDERS_PATTERN, (string) $phaseId)
        );
        if ($data == null) {
            return PhaseRepositoryLoadResult::err(PhaseRepositoryLoadResult::E_NOT_FOUND);
        }

        return PhaseRepositoryLoadResult::ok($data);
    }

    public function saveSvgWithOrders(PhaseId $phaseId, string $svg): void
    {
        $path = sprintf(self::SVG_WITH_ORDERS_PATTERN, (string) $phaseId);
        if ($this->filesystem->exists($path)) {
            throw new AlreadyPresentException($path);
        }

        $this->filesystem->put(
            sprintf(self::SVG_WITH_ORDERS_PATTERN, (string) $phaseId),
            $svg
        );
    }

    public function loadLinkToSvgWithOrders(PhaseId $phaseId): PhaseRepositoryLoadResult
    {
        if (! $this->filesystem->exists(sprintf(self::SVG_WITH_ORDERS_PATTERN, (string) $phaseId))) {
            return PhaseRepositoryLoadResult::err(PhaseRepositoryLoadResult::E_NOT_FOUND);
        }

        $url = $this->filesystem->url(sprintf(self::SVG_WITH_ORDERS_PATTERN, (string) $phaseId));

        return PhaseRepositoryLoadResult::ok($url);
    }

    public function loadAdjudicatedSvg(PhaseId $phaseId): PhaseRepositoryLoadResult
    {
        $data = $this->filesystem->get(
            sprintf(self::ADJUDICATED_SVG_PATTERN, (string) $phaseId)
        );
        if ($data == null) {
            return PhaseRepositoryLoadResult::err(PhaseRepositoryLoadResult::E_NOT_FOUND);
        }

        return PhaseRepositoryLoadResult::ok($data);

    }

    public function saveAdjudicatedSvg(PhaseId $phaseId, string $svg): void
    {
        $path = sprintf(self::ADJUDICATED_SVG_PATTERN, (string) $phaseId);
        if ($this->filesystem->exists($path)) {
            throw new AlreadyPresentException($path);
        }

        $this->filesystem->put(
            sprintf(self::ADJUDICATED_SVG_PATTERN, (string) $phaseId),
            $svg
        );
    }

    public function loadLinkToAdjudicatedSvg(PhaseId $phaseId): PhaseRepositoryLoadResult
    {
        if (! $this->filesystem->exists(sprintf(self::ADJUDICATED_SVG_PATTERN, (string) $phaseId))) {
            return PhaseRepositoryLoadResult::err(PhaseRepositoryLoadResult::E_NOT_FOUND);
        }

        $url = $this->filesystem->url(sprintf(self::ADJUDICATED_SVG_PATTERN, (string) $phaseId));

        return PhaseRepositoryLoadResult::ok($url);
    }
}
