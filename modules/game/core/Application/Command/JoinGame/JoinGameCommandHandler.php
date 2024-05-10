<?php

namespace Dnw\Game\Core\Application\Command\JoinGame;

use Dnw\Foundation\Identity\Id;
use Dnw\Game\Core\Domain\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\ValueObject\PlayerId;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantPower\VariantPowerId;

readonly class JoinGameCommandHandler
{
    public function __construct(
        private GameRepositoryInterface $gameRepository
    ) {

    }

    public function handle(JoinGameCommand $command): void
    {
        $game = $this->gameRepository->load(GameId::fromString($command->gameId));

        $game->join(
            PlayerId::fromId($command->userId),
            $command->variantPowerId->map(fn (Id $id) => VariantPowerId::fromId($id))
        );

        $this->gameRepository->save($game);
    }
}
