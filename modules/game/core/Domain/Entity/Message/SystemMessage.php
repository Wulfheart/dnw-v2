<?php

namespace Dnw\Game\Core\Domain\Entity\Message;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\ValueObject\MessageRoom\MessageContent;
use Dnw\Game\Core\Domain\ValueObject\MessageRoom\MessageId;

class SystemMessage
{
    public function __construct(
        public MessageId $id,
        public MessageContent $content,
        public CarbonImmutable $sentAt,
    ) {

    }
}
