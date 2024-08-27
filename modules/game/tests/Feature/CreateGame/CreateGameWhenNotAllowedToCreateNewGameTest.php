<?php

namespace Dnw\Game\Tests\Feature\CreateGame;

use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\TestCase;

#[CoversNothing]
#[AllowLaravelTestCase]
class CreateGameWhenNotAllowedToCreateNewGameTest extends TestCase
{
    public function test_cannot_join_due_to_maximum_number_of_parallel_games(): void {}
}
