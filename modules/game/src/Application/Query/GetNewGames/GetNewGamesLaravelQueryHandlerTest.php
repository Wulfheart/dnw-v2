<?php

namespace Dnw\Game\Application\Query\GetNewGames;

use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Domain\Game\Test\Factory\GameBuilder;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\ModuleTestCase;

#[CoversClass(GetNewGamesLaravelQueryHandler::class)]
class GetNewGamesLaravelQueryHandlerTest extends ModuleTestCase
{
    public function test(): void
    {
        $gameRepository = $this->bootstrap(GameRepositoryInterface::class);

        $playerId = PlayerId::new();
        $this->bindUser($playerId->toId());

        $newGameId1 = GameId::new();
        $newGameId2 = GameId::new();

        $games = [
            GameBuilder::initialize()->storeInitialAdjudication()->start(),
            GameBuilder::initialize(gameId: $newGameId1)->storeInitialAdjudication(),
            GameBuilder::initialize(gameId: $newGameId2)->storeInitialAdjudication(),
            GameBuilder::initialize(),
            GameBuilder::initialize()->finish(),
            GameBuilder::initialize(playerId: $playerId)->finish(),
            GameBuilder::initialize()->join($playerId),
        ];

        $states = collect($games)->map(fn (GameBuilder $game) => $game->build()->gameId)->toArray();

        foreach ($games as $game) {
            $gameRepository->save($game->build());
        }

        $result = $this->bus->handle(new GetNewGamesQuery(5, 0));

        $res = $result->games->every(function (NewGameInfo $game) use ($newGameId1, $newGameId2) {
            return $game->gameInfo->id->equals($newGameId1->toId()) || $game->gameInfo->id->equals($newGameId2->toId());
        });

        $this->assertCount(2, $result->games);
        $this->assertTrue($res, 'Command does not return only the desired games');

    }
}
