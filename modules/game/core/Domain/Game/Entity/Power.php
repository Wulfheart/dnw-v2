<?php

namespace Dnw\Game\Core\Domain\Game\Entity;

use Dnw\Game\Core\Domain\Game\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantPower\VariantPowerId;
use DomainException;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

class Power
{
    public function __construct(
        public PowerId $powerId,
        /** @var Option<PlayerId> $playerId */
        public Option $playerId,
        public VariantPowerId $variantPowerId,
    ) {

    }

    public function assign(PlayerId $playerId): void
    {
        if ($this->playerId->isDefined()) {
            throw new DomainException(
                "Power $this->powerId already assigned to player $playerId because {$this->playerId->get()} is already assigned"
            );
        }
        $this->playerId = Some::create($playerId);
    }

    public function unassign(): void
    {
        if ($this->playerId->isEmpty()) {
            throw new DomainException("Power $this->powerId is not assigned to a player");
        }
        $this->playerId = None::create();
    }
}
