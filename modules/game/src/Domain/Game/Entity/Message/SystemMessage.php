<?php

namespace Dnw\Game\Domain\Game\Entity\Message;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Domain\Game\ValueObject\MessageRoom\MessageContent;
use Dnw\Game\Domain\Game\ValueObject\MessageRoom\MessageId;

class SystemMessage
{
    public function __construct(
        public MessageId $id,
        public MessageContent $content,
        public DateTime $sentAt,
    ) {}
}
