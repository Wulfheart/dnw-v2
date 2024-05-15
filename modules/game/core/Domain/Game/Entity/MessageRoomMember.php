<?php

namespace Dnw\Game\Core\Domain\Game\Entity;

use Dnw\Game\Core\Domain\Game\ValueObject\MessageRoom\MessageRoomMemberId;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;

class MessageRoomMember
{
    public function __construct(
        public MessageRoomMemberId $id,
        public PowerId $powerId,
        public bool $isOwner,
    ) {

    }
}
