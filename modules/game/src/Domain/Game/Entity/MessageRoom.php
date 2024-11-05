<?php

namespace Dnw\Game\Domain\Game\Entity;

use Dnw\Foundation\Exception\DomainException;
use Dnw\Game\Domain\Game\Collection\MessageRoomMemberCollection;
use Dnw\Game\Domain\Game\Entity\Message\PowerMessage;
use Dnw\Game\Domain\Game\Entity\Message\SystemMessage;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Game\ValueObject\MessageRoom\MessageRoomId;
use Dnw\Game\Domain\Game\ValueObject\MessageRoom\MessageRoomName;
use Wulfheart\Option\Option;

class MessageRoom
{
    /** @var array<PowerMessage|SystemMessage> */
    private array $messages = [];

    public function __construct(
        public MessageRoomId $id,
        /** @var Option<MessageRoomName> $messageRoomName */
        public Option $messageRoomName,
        public MessageRoomMemberCollection $memberCollection,
        public bool $isGroup,
        public Count $messagesCount,
    ) {}

    public function addMessage(PowerMessage|SystemMessage $message): void
    {
        if ($message instanceof PowerMessage && ! $this->memberCollection->hasPowerAdMember($message->sender)) {
            throw new DomainException('The sender is not a member of this room');
        }
        $this->messages[] = $message;
    }

    /**
     * @return array<PowerMessage|SystemMessage>
     */
    public function releaseMessages(): array
    {
        $messages = $this->messages;
        $this->messages = [];

        return $messages;
    }
}
