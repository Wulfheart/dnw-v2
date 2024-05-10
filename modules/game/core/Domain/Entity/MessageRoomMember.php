<?php

namespace Dnw\Game\Core\Domain\Entity;

use Dnw\Game\Core\Domain\ValueObject\MessageRoom\MessageRoomMemberId;
use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;

class MessageRoomMember
{
    public function __construct(
        public MessageRoomMemberId $id,
        public PowerId $powerId,
        public bool $isOwner,
    ) {

    }
}
