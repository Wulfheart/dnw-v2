<?php

namespace Dnw\Game\Application\Command\CreateGame;

use PHPUnit\Framework\Attributes\CoversClass;
use Tests\LaravelTestCase;

#[CoversClass(CreateGameCommand::class)]
#[CoversClass(CreateGameCommandHandler::class)]

class CreateGameTest extends LaravelTestCase
{
    public function test_cannot_create_a_game_when_user_has_already_three_games_they_are_playing_in(): void
    {
        $this->markTestSkipped();
    }

    public function test_cannot_create_a_game_with_an_unloadable_variant(): void
    {
        $this->markTestSkipped();
    }

    public function test_can_create_a_game(): void
    {
        $this->markTestSkipped();
    }
}
