<?php

namespace App\Web\Game\GamePanel;

use Dnw\Foundation\Identity\Id;
use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\HttpTestCase;

#[CoversNothing]
class GamePanelControllerTest extends HttpTestCase
{
    public function test_shows_created_view(): void
    {
        $response = $this->actingAs($this->randomUser())->get(action([GamePanelController::class, 'show'], ['id' => Id::generate()]));
        $response->assertStatus(404);
    }
}
