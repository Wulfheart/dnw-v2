<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Phase;

use Dnw\Foundation\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Game\Exception\AlreadyPresentException;
use Dnw\Game\Core\Domain\Game\Repository\PhaseRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;
use Illuminate\Contracts\Filesystem\Filesystem;

class LaravelPhaseRepository implements PhaseRepositoryInterface
{
    private const ENCODED_STATE_PATTERN = '/%s/encoded_state.json';

    private const SVG_WITH_ORDERS_PATTERN = '/%s/svg_with_orders.svg';

    private const ADJUDICATED_SVG_PATTERN = '/%s/adjudicated_svg.svg';

    public function __construct(
        private Filesystem $filesystem,
    ) {}

    public function loadEncodedState(PhaseId $phaseId): string
    {
        return $this->filesystem->get(
            sprintf(self::ENCODED_STATE_PATTERN, (string) $phaseId)
        ) ?? throw new NotFoundException();
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

    public function loadSvgWithOrders(PhaseId $phaseId): string
    {
        return $this->filesystem->get(
            sprintf(self::SVG_WITH_ORDERS_PATTERN, (string) $phaseId)
        ) ?? throw new NotFoundException();
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

    public function loadAdjudicatedSvg(PhaseId $phaseId): string
    {
        return $this->filesystem->get(
            sprintf(self::ADJUDICATED_SVG_PATTERN, (string) $phaseId)
        ) ?? throw new NotFoundException();

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
}
