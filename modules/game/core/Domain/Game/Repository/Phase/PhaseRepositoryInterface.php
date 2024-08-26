<?php

namespace Dnw\Game\Core\Domain\Game\Repository\Phase;

use Dnw\Foundation\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Game\Exception\AlreadyPresentException;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;

interface PhaseRepositoryInterface
{
    /**
     * @throws NotFoundException
     */
    public function loadEncodedState(PhaseId $phaseId): string;

    /**
     * @throws AlreadyPresentException
     */
    public function saveEncodedState(PhaseId $phaseId, string $encodedState): void;

    /**
     * @throws NotFoundException
     */
    public function loadSvgWithOrders(PhaseId $phaseId): string;

    /**
     * @throws AlreadyPresentException
     */
    public function saveSvgWithOrders(PhaseId $phaseId, string $svg): void;

    /**
     * @throws NotFoundException
     */
    public function loadAdjudicatedSvg(PhaseId $phaseId): string;

    /**
     * @throws AlreadyPresentException
     */
    public function saveAdjudicatedSvg(PhaseId $phaseId, string $svg): void;
}
