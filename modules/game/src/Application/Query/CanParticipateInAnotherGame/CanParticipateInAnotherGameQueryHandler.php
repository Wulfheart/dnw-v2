<?php

namespace Dnw\Game\Application\Query\CanParticipateInAnotherGame;

use Dnw\Game\Domain\Player\Repository\Player\PlayerRepositoryInterface;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;

final readonly class CanParticipateInAnotherGameQueryHandler
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepository
    ) {}

    public function handle(CanParticipateInAnotherGameQuery $query): bool
    {
        $player = $this->playerRepository->load(PlayerId::fromId($query->playerId));

        return $player->unwrap()->canParticipateInAnotherGame()->passes();
    }
}
