<?php

namespace Entity;

use PhpOption\Option;
use ValueObjects\MessageMode\MessageModeId;
use ValueObjects\MessageMode\MessageModeName;

final readonly class MessageMode
{
    public function __construct(
        /** @var Option<MessageModeName> $messageModeId */
        public Option $name,
        /** @var Option<MessageModeId> $messageModeId */
        public ?MessageModeId $messageModeId,
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
