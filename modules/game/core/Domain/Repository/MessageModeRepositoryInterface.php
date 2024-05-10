<?php

namespace Dnw\Game\Core\Domain\Repository;

use Dnw\Game\Core\Domain\Entity\MessageMode;
use Dnw\Game\Core\Domain\Exception\NotFoundException;
use Dnw\Game\Core\Domain\ValueObject\MessageMode\MessageModeId;

interface MessageModeRepositoryInterface
{
    /**
     * @throws NotFoundException
     */
    public function load(MessageModeId $messageModeId): MessageMode;
}
