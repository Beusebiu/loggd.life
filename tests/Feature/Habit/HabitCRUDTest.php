<?php

namespace Tests\Feature\Habit;

use Tests\TestCase;

class HabitCRUDTest extends TestCase
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
