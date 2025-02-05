<?php

namespace Dnw\Game\Application\Query\CanParticipateInAnotherGame;

use Dnw\Game\Domain\Player\Test\PlayerBuilder;
use Dnw\Game\Infrastructure\Repository\Player\SimpleInMemoryPlayerRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CanParticipateInAnotherGameQueryHandler::class)]
class CanParticipateInAnotherGameQueryHandlerTest extends TestCase
{
    public function test_if_player_can_join_returns_true(): void
    {
        $player = PlayerBuilder::initialize()->numberOfGamesIsOk()->build();
        $repo = new SimpleInMemoryPlayerRepository([$player]);

        $handler = new CanParticipateInAnotherGameQueryHandler($repo);

        $this->assertTrue($handler->handle(new CanParticipateInAnotherGameQuery($player->playerId->toId())));
    }

    public function test_if_player_can_join_returns_false(): void
    {
        $player = PlayerBuilder::initialize()->inTooManyGames()->build();
        $repo = new SimpleInMemoryPlayerRepository([$player]);

        $handler = new CanParticipateInAnotherGameQueryHandler($repo);

        $this->assertFalse($handler->handle(new CanParticipateInAnotherGameQuery($player->playerId->toId())));
    }
}
