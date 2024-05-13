<?php

namespace Dnw\Game\Core\Domain\Repository;

use Dnw\Game\Core\Domain\ValueObject\Phase\PhaseId;

interface PhaseRepositoryInterface
{
    public function loadEncodedState(PhaseId $phaseId): string;
}
