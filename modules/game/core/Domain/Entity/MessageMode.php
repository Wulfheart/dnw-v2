<?php

namespace Dnw\Game\Core\Domain\Entity;

use Dnw\Game\Core\Domain\ValueObject\MessageMode\MessageModeId;
use Dnw\Game\Core\Domain\ValueObject\MessageMode\MessageModeName;
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
        public bool $allowCreationOfGroupChats,
        public bool $allowAdjustmentMessages,
        public bool $allowMoveMessages,
        public bool $allowRetreatMessages,
        public bool $allowPreGameMessages,
        public bool $allowPostGameMessages,
    ) {
    }
}
