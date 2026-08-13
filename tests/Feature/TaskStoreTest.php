<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_can_store_a_task_successfully()
    {
        $this->withoutExceptionHandling();

        $payload = [
            'task_name'       => 'Laporan Akhir Testing',
            'deadline'        => '2026-08-15 10:00:00',
            'estimated_hours' => 3,
            'difficulty'      => 'hard',
        ];

        $response = $this->post(route('tasks.store'), $payload);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'task_name'       => 'Laporan Akhir Testing',
            'estimated_hours' => 3,
            'difficulty'      => 'hard',
        ]);
    }
}
