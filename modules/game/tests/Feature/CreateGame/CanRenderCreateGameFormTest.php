<?php

namespace Dnw\Game\Test\Feature\CreateGame;

use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\LaravelTestCase;

#[CoversNothing]

class CanRenderCreateGameFormTest extends LaravelTestCase
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
