<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Phase;

use Dnw\Game\Core\Domain\Game\Exception\AlreadyPresentException;
use Dnw\Game\Core\Domain\Game\Repository\Phase\PhaseRepositoryInterface;
use Dnw\Game\Core\Domain\Game\Repository\Phase\PhaseRepositoryLoadResult;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;
use Exception;

class InMemoryPhaseRepository implements PhaseRepositoryInterface
{
    public function __construct(
        /** @var array<string, string> $encodedStates */
        private array $encodedStates = [],
        /** @var array<string, string> $svgsWithOrders */
        private array $svgsWithOrders = [],
        /** @var array<string, string> $adjudicatedSvgs */
        private array $adjudicatedSvgs = []
    ) {}

    public function loadEncodedState(PhaseId $phaseId): PhaseRepositoryLoadResult
    {
        if (! isset($this->encodedStates[(string) $phaseId])) {
            return PhaseRepositoryLoadResult::err(PhaseRepositoryLoadResult::E_NOT_FOUND);
        }

        return PhaseRepositoryLoadResult::ok($this->encodedStates[(string) $phaseId]);
    }

    public function saveEncodedState(PhaseId $phaseId, string $encodedState): void
    {
        if (isset($this->encodedStates[(string) $phaseId])) {
            throw new AlreadyPresentException();
        }
        $this->encodedStates[(string) $phaseId] = $encodedState;
    }

    public function loadSvgWithOrders(PhaseId $phaseId): PhaseRepositoryLoadResult
    {
        if (! isset($this->svgsWithOrders[(string) $phaseId])) {
            return PhaseRepositoryLoadResult::err(PhaseRepositoryLoadResult::E_NOT_FOUND);
        }

        return PhaseRepositoryLoadResult::ok($this->svgsWithOrders[(string) $phaseId]);
    }

    public function saveSvgWithOrders(PhaseId $phaseId, string $svg): void
    {
        if (isset($this->svgsWithOrders[(string) $phaseId])) {
            throw new AlreadyPresentException();
        }
        $this->svgsWithOrders[(string) $phaseId] = $svg;
    }

    /**
     * @codeCoverageIgnore
     */
    public function loadLinkToSvgWithOrders(PhaseId $phaseId): PhaseRepositoryLoadResult
    {
        throw new Exception('Not implemented');
    }

    public function loadAdjudicatedSvg(PhaseId $phaseId): PhaseRepositoryLoadResult
    {
        if (! isset($this->adjudicatedSvgs[(string) $phaseId])) {
            return PhaseRepositoryLoadResult::err(PhaseRepositoryLoadResult::E_NOT_FOUND);
        }

        return PhaseRepositoryLoadResult::ok($this->adjudicatedSvgs[(string) $phaseId]);
    }

    public function saveAdjudicatedSvg(PhaseId $phaseId, string $svg): void
    {
        if (isset($this->adjudicatedSvgs[(string) $phaseId])) {
            throw new AlreadyPresentException();
        }
        $this->adjudicatedSvgs[(string) $phaseId] = $svg;
    }

    /**
     * @codeCoverageIgnore
     */
    public function loadLinkToAdjudicatedSvg(PhaseId $phaseId): PhaseRepositoryLoadResult
    {
        throw new Exception('Not implemented');
    }
}
