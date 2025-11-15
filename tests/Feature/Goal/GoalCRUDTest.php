<?php

namespace Tests\Feature\Goal;

use Tests\TestCase;

class GoalCRUDTest extends TestCase
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
