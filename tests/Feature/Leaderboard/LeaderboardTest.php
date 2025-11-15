<?php

namespace Tests\Feature\Leaderboard;

use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
