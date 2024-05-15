<?php

namespace Dnw\Game\Core\Domain\Game\Repository;

use Dnw\Game\Core\Domain\Game\Entity\MessageMode;
use Dnw\Game\Core\Domain\Game\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Game\ValueObject\MessageMode\MessageModeId;

interface MessageModeRepositoryInterface
{
    /**
     * @throws NotFoundException
     */
    public function load(MessageModeId $messageModeId): MessageMode;
}
