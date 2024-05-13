<?php

namespace Dnw\Game\Core\Domain\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\Entity\MessageRoomMember;
use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;

/**
 * @extends Collection<MessageRoomMember>
 */
class MessageRoomMemberCollection extends Collection
{
    public function hasPowerAdMember(PowerId $powerId): bool
    {
        return $this->findBy(
            fn (MessageRoomMember $member) => $member->powerId === $powerId
        )->isDefined();
    }
}
