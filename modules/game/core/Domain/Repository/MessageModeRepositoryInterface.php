<?php

namespace Dnw\Game\Core\Domain\Repository;

use Dnw\Game\Core\Domain\Entity\MessageMode;
use Dnw\Game\Core\Domain\ValueObject\MessageMode\MessageModeId;

interface MessageModeRepositoryInterface
{
    public function find(MessageModeId $messageModeId): MessageMode;
}
