<?php

namespace Dnw\Game\Core\Domain\Entity;

use Dnw\Game\Core\Domain\Collection\MessageRoomMemberCollection;
use Dnw\Game\Core\Domain\Entity\Message\PowerMessage;
use Dnw\Game\Core\Domain\Entity\Message\SystemMessage;
use Dnw\Game\Core\Domain\Exception\DomainException;
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
        /** @var Option<MessageRoomName> $messageRoomName */
        public Option $messageRoomName,
        public MessageRoomMemberCollection $memberCollection,
        public bool $isGroup,
        public Count $messagesCount,
    ) {

    }

    public function addMessage(PowerMessage|SystemMessage $message): void
    {
        if ($message instanceof PowerMessage && ! $this->memberCollection->contains($message->sender)) {
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
