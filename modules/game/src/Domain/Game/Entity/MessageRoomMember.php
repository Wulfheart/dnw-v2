<?php

namespace Dnw\Game\Domain\Game\Entity;

use Dnw\Game\Domain\Game\ValueObject\MessageRoom\MessageRoomMemberId;
use Dnw\Game\Domain\Game\ValueObject\Power\PowerId;

class MessageRoomMember
{
    public function __construct(
        public MessageRoomMemberId $id,
        public PowerId $powerId,
        public bool $isOwner,
    ) {}
}
