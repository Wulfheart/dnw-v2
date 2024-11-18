<?php

namespace Dnw\Game\Domain\Game\Repository\Phase;

use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseId;

interface PhaseRepositoryInterface
{
    public function loadEncodedState(PhaseId $phaseId): PhaseRepositoryLoadResult;

    public function saveEncodedState(PhaseId $phaseId, string $encodedState): PhaseRepositorySaveResult;

    public function loadSvgWithOrders(PhaseId $phaseId): PhaseRepositoryLoadResult;

    public function saveSvgWithOrders(PhaseId $phaseId, string $svg): PhaseRepositorySaveResult;

    public function loadLinkToSvgWithOrders(PhaseId $phaseId): PhaseRepositoryLoadResult;

    public function loadAdjudicatedSvg(PhaseId $phaseId): PhaseRepositoryLoadResult;

    public function saveAdjudicatedSvg(PhaseId $phaseId, string $svg): PhaseRepositorySaveResult;

    public function loadLinkToAdjudicatedSvg(PhaseId $phaseId): PhaseRepositoryLoadResult;
}
