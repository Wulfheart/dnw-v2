<?php

namespace App\Web\Game\GamePanel;

use Dnw\Foundation\Identity\Id;
use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\TestCase;

#[CoversNothing]
#[AllowLaravelTestCase]
class GamePanelControllerTest extends TestCase
{
    public function test_shows_created_view(): void
    {
        $response = $this->actingAs($this->randomUser())->get(action([GamePanelController::class, 'show'], ['id' => Id::generate()]));
        $response->assertStatus(404);
    }
}
