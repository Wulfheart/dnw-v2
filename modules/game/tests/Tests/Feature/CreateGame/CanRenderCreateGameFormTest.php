<?php

namespace Dnw\Game\Tests\Feature\CreateGame;

use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\TestCase;

#[CoversNothing]
#[AllowLaravelTestCase]
class CanRenderCreateGameFormTest extends TestCase
{
    public function test_authenticated(): void
    {
        $response = $this->actingAs($this->randomUser())->get(route('game.create'));

        $response->assertOk();
    }

    public function test_unauthenticated(): void
    {
        $response = $this->get(route('game.create'));

        $response->assertStatus(302);
    }
}
