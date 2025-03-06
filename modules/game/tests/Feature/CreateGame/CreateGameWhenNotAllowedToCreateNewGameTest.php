<?php

namespace Dnw\Game\Test\Feature\CreateGame;

use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\LaravelTestCase;

#[CoversNothing]
#[AllowLaravelTestCase]
class CreateGameWhenNotAllowedToCreateNewGameTest extends LaravelTestCase
{
    public function test_cannot_join_due_to_maximum_number_of_parallel_games(): void
    {
        $this->markTestSkipped('Not implemented yet.');
    }
}
