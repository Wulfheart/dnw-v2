<?php

namespace Dnw\Game\Domain\Game\Repository\Phase;

use Dnw\Game\Domain\Game\Exception\AlreadyPresentException;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseId;

interface PhaseRepositoryInterface
{
    public function loadEncodedState(PhaseId $phaseId): PhaseRepositoryLoadResult;

    /**
     * @throws AlreadyPresentException
     */
    public function saveEncodedState(PhaseId $phaseId, string $encodedState): void;

    public function loadSvgWithOrders(PhaseId $phaseId): PhaseRepositoryLoadResult;

    /**
     * @throws AlreadyPresentException
     */
    public function saveSvgWithOrders(PhaseId $phaseId, string $svg): void;

    public function loadLinkToSvgWithOrders(PhaseId $phaseId): PhaseRepositoryLoadResult;

    public function loadAdjudicatedSvg(PhaseId $phaseId): PhaseRepositoryLoadResult;

    /**
     * @throws AlreadyPresentException
     */
    public function saveAdjudicatedSvg(PhaseId $phaseId, string $svg): void;

    public function loadLinkToAdjudicatedSvg(PhaseId $phaseId): PhaseRepositoryLoadResult;
}
