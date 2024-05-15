<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Phase;

use Dnw\Foundation\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Game\Exception\AlreadyPresentException;
use Dnw\Game\Core\Domain\Game\Repository\PhaseRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;

class InMemoryPhaseRepository implements PhaseRepositoryInterface
{
    public function __construct(
        /** @var array<string, string> $encodedStates */
        private array $encodedStates = [],
        /** @var array<string, string> $svgsWithOrders */
        private array $svgsWithOrders = [],
        /** @var array<string, string> $adjudicatedSvgs */
        private array $adjudicatedSvgs = []
    ) {
    }

    public function loadEncodedState(PhaseId $phaseId): string
    {
        return $this->encodedStates[(string) $phaseId] ?? throw new NotFoundException();
    }

    public function saveEncodedState(PhaseId $phaseId, string $encodedState): void
    {
        if (isset($this->encodedStates[(string) $phaseId])) {
            throw new AlreadyPresentException();
        }
        $this->encodedStates[(string) $phaseId] = $encodedState;
    }

    public function loadSvgWithOrders(PhaseId $phaseId): string
    {
        return $this->svgsWithOrders[(string) $phaseId] ?? throw new NotFoundException();
    }

    public function saveSvgWithOrders(PhaseId $phaseId, string $svg): void
    {
        if (isset($this->svgsWithOrders[(string) $phaseId])) {
            throw new AlreadyPresentException();
        }
        $this->svgsWithOrders[(string) $phaseId] = $svg;
    }

    public function loadAdjudicatedSvg(PhaseId $phaseId): string
    {
        return $this->adjudicatedSvgs[(string) $phaseId] ?? throw new NotFoundException();
    }

    public function saveAdjudicatedSvg(PhaseId $phaseId, string $svg): void
    {
        if (isset($this->adjudicatedSvgs[(string) $phaseId])) {
            throw new AlreadyPresentException();
        }
        $this->adjudicatedSvgs[(string) $phaseId] = $svg;
    }
}
