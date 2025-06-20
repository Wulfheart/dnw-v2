<?php

namespace Dnw\Game\Domain\Game\Repository\Phase\Impl\Laravel;

use Dnw\Game\Domain\Game\Repository\Phase\PhaseRepositoryInterface;
use Dnw\Game\Domain\Game\Repository\Phase\PhaseRepositoryLoadResult;
use Dnw\Game\Domain\Game\Repository\Phase\PhaseRepositorySaveResult;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseId;
use Illuminate\Contracts\Filesystem\Filesystem;

class LaravelPhaseRepository implements PhaseRepositoryInterface
{
    private const string ENCODED_STATE_PATTERN = 'phases/%s/encoded_state.json';

    private const string SVG_WITH_ORDERS_PATTERN = 'phases/%s/svg_with_orders.svg';

    private const string ADJUDICATED_SVG_PATTERN = 'phases/%s/adjudicated_svg.svg';

    private const string PUBLIC_URL_PATTERN = '/storage/%s';

    public function __construct(
        private readonly Filesystem $filesystem,
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

    public function saveEncodedState(PhaseId $phaseId, string $encodedState): PhaseRepositorySaveResult
    {
        $path = sprintf(self::ENCODED_STATE_PATTERN, (string) $phaseId);
        if ($this->filesystem->exists($path)) {
            return PhaseRepositorySaveResult::err(PhaseRepositorySaveResult::E_ALREADY_PRESENT);
        }

        $this->filesystem->put(
            sprintf(self::ENCODED_STATE_PATTERN, (string) $phaseId),
            $encodedState
        );

        return PhaseRepositorySaveResult::ok();
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

    public function saveSvgWithOrders(PhaseId $phaseId, string $svg): PhaseRepositorySaveResult
    {
        $path = sprintf(self::SVG_WITH_ORDERS_PATTERN, (string) $phaseId);
        if ($this->filesystem->exists($path)) {
            return PhaseRepositorySaveResult::err(PhaseRepositorySaveResult::E_ALREADY_PRESENT);
        }

        $this->filesystem->put(
            sprintf(self::SVG_WITH_ORDERS_PATTERN, (string) $phaseId),
            $svg
        );

        return PhaseRepositorySaveResult::ok();
    }

    public function loadLinkToSvgWithOrders(PhaseId $phaseId): PhaseRepositoryLoadResult
    {
        if (! $this->filesystem->exists(sprintf(self::SVG_WITH_ORDERS_PATTERN, (string) $phaseId))) {
            return PhaseRepositoryLoadResult::err(PhaseRepositoryLoadResult::E_NOT_FOUND);
        }

        $url = $this->toUrl(sprintf(self::SVG_WITH_ORDERS_PATTERN, (string) $phaseId));

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

    public function saveAdjudicatedSvg(PhaseId $phaseId, string $svg): PhaseRepositorySaveResult
    {
        $path = sprintf(self::ADJUDICATED_SVG_PATTERN, (string) $phaseId);
        if ($this->filesystem->exists($path)) {
            return PhaseRepositorySaveResult::err(PhaseRepositorySaveResult::E_ALREADY_PRESENT);
        }

        $this->filesystem->put(
            sprintf(self::ADJUDICATED_SVG_PATTERN, (string) $phaseId),
            $svg
        );

        return PhaseRepositorySaveResult::ok();
    }

    public function loadLinkToAdjudicatedSvg(PhaseId $phaseId): PhaseRepositoryLoadResult
    {
        if (! $this->filesystem->exists(sprintf(self::ADJUDICATED_SVG_PATTERN, (string) $phaseId))) {
            return PhaseRepositoryLoadResult::err(PhaseRepositoryLoadResult::E_NOT_FOUND);
        }

        $url = $this->toUrl(sprintf(self::ADJUDICATED_SVG_PATTERN, (string) $phaseId));

        return PhaseRepositoryLoadResult::ok($url);
    }

    private function toUrl(string $path): string
    {
        return sprintf(
            self::PUBLIC_URL_PATTERN,
            $path
        );
    }
}
