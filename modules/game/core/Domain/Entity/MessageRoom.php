<?php

namespace Dnw\Game\Core\Domain\Entity;

use Dnw\Game\Core\Domain\Collection\MessageRoomMemberCollection;
use Dnw\Game\Core\Domain\Entity\Message\PowerMessage;
use Dnw\Game\Core\Domain\Entity\Message\SystemMessage;
use Dnw\Game\Core\Domain\ValueObject\Count;
use Dnw\Game\Core\Domain\ValueObject\MessageRoom\MessageRoomId;
use Dnw\Game\Core\Domain\ValueObject\MessageRoom\MessageRoomName;
use PhpOption\Option;

class MessageRoom
{
    /** @var array<PowerMessage|SystemMessage> */
    private array $messages = [];

    public function __construct(
        public MessageRoomId $id,
        /** @var Option<MessageRoomName> $name */
        public Option $messageRoomName,
        public MessageRoomMemberCollection $members,
        public bool $isGroup,
        public Count $messagesCount,
    ) {

    }

    public function addMessage()
    {

    }
}
