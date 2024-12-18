<?php

namespace Dnw\Game\Application\Query\GetGameById;

use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Game\Domain\Game\Repository\Game\InMemoryGameRepository;
use Dnw\Game\Domain\Game\Test\Factory\GameBuilder;
use Dnw\Game\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Infrastructure\Repository\Variant\InMemoryVariantRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Wulfheart\Option\ResultAsserter;

#[CoversClass(GetGameByIdQueryHandler::class)]
class GetGameByIdQueryHandlerTest extends TestCase
{
    public function test_handle_game_created(): void
    {
        $variant = VariantFactory::standard();
        $game = GameBuilder::initialize(variant: $variant)->build();

        $gameRepo = new InMemoryGameRepository(new FakeEventDispatcher(), [$game]);
        $variantRepo = new InMemoryVariantRepository([$variant]);

        $handler = new GetGameByIdQueryHandler($gameRepo, $variantRepo, new NullLogger());

        $result = $handler->handle(new GetGameByIdQuery($game->gameId->toId()));
        ResultAsserter::assertOk($result);

    }
}
