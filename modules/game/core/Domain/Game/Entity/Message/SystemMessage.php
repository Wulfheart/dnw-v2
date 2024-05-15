<?php

namespace Dnw\Game\Core\Domain\Game\Entity\Message;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Game\ValueObject\MessageRoom\MessageContent;
use Dnw\Game\Core\Domain\Game\ValueObject\MessageRoom\MessageId;

class SystemMessage
{
    public function __construct(
        public MessageId $id,
        public MessageContent $content,
        public CarbonImmutable $sentAt,
    ) {

    }
}
