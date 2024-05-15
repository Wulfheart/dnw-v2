<?php

namespace Dnw\Game\Core\Domain\Game\Entity;

use Dnw\Game\Core\Domain\Game\Exception\DomainException;
use Dnw\Game\Core\Domain\Game\ValueObject\MessageMode\MessageModeId;
use Dnw\Game\Core\Domain\Game\ValueObject\MessageMode\MessageModeName;
use PhpOption\Option;

readonly class MessageMode
{
    public function __construct(
        /** @var Option<MessageModeName> $name */
        public Option $name,
        /** @var Option<MessageModeId> $messageModeId */
        public Option $messageModeId,
        public bool $isCustom,
        public string $description,
        public bool $isAnonymous,
        public bool $allowOnlyPublicMessages,
        public bool $allowCreationOfGroupChats,
        public bool $allowAdjustmentMessages,
        public bool $allowMoveMessages,
        public bool $allowRetreatMessages,
        public bool $allowPreGameMessages,
        public bool $allowPostGameMessages,
    ) {
        if ($this->allowOnlyPublicMessages && ! $this->isAnonymous) {
            throw new DomainException('Only public messages are allowed in non-anonymous games');
        }
    }
}
