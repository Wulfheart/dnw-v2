<?php

namespace Dnw\Game\Core\Domain\Repository;

use Dnw\Game\Core\Domain\Exception\AlreadyPresentException;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhaseId;

interface PhaseRepositoryInterface
{
    public function loadEncodedState(PhaseId $phaseId): string;

    /**
     * @throws AlreadyPresentException
     */
    public function saveEncodedState(PhaseId $phaseId, string $encodedState): void;

    /**
     * @throws AlreadyPresentException
     */
    public function saveSvgWithOrders(PhaseId $phaseId, string $svg): void;

    /**
     * @throws AlreadyPresentException
     */
    public function saveAdjudicatedSvg(PhaseId $phaseId, string $svg): void;
}
