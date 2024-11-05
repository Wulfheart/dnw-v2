<?php

namespace Dnw\Game\Domain\Game\Repository;

use Dnw\Foundation\Exception\NotFoundException;
use Dnw\Game\Domain\Game\Entity\MessageMode;
use Dnw\Game\Domain\Game\ValueObject\MessageMode\MessageModeId;

interface MessageModeRepositoryInterface
{
    /**
     * @throws NotFoundException
     */
    public function load(MessageModeId $messageModeId): MessageMode;
}
