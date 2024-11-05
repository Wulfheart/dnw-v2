<?php

namespace Dnw\Game\Domain\Game\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Domain\Game\Entity\MessageRoomMember;
use Dnw\Game\Domain\Game\ValueObject\Power\PowerId;

/**
 * @extends Collection<MessageRoomMember>
 */
class MessageRoomMemberCollection extends Collection
{
    public function hasPowerAdMember(PowerId $powerId): bool
    {
        return $this->findBy(
            fn (MessageRoomMember $member) => $member->powerId === $powerId
        )->isSome();
    }
}
