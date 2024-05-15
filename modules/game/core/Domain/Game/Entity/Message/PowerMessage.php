<?php

namespace Dnw\Game\Core\Domain\Game\Entity\Message;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Game\ValueObject\MessageRoom\MessageContent;
use Dnw\Game\Core\Domain\Game\ValueObject\MessageRoom\MessageId;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;

class PowerMessage
{
    public function __construct(
        public MessageId $id,
        public MessageContent $content,
        public PowerId $sender,
        public CarbonImmutable $sentAt,
    ) {

    }
}
