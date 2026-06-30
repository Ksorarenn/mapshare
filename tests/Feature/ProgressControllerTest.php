<?php

namespace Tests\Feature;

use App\Models\Roadmap;
use App\Models\User;
use App\Models\UserRoadmapProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_successful_progress_storage()
    {
        $user = User::create(['name' => 'Student', 'email' => 'student@test.com', 'password' => '123']);
        $roadmap = Roadmap::create(['user_id' => $user->id, 'title' => 'Laravel Basic', 'graph_data' => []]);

        $this->actingAs($user);

        $response = $this->postJson('/progress', [
            'roadmap_id' => $roadmap->id,
            'completed_nodes' => ['node_1', 'node_2'],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('user_roadmap_progress', [
            'user_id' => $user->id,
            'roadmap_id' => $roadmap->id,
        ]);
    }

    /** @test */
    public function test_progress_storage_validation_fails()
    {
        $user = User::create(['name' => 'Student', 'email' => 'student@test.com', 'password' => '123']);
        $this->actingAs($user);

        // Передаем строку вместо массива строк в completed_nodes
        $response = $this->postJson('/progress', [
            'roadmap_id' => 1,
            'completed_nodes' => 'not-an-array',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function test_get_existing_progress()
    {
        $user = User::create(['name' => 'Student', 'email' => 'student@test.com', 'password' => '123']);
        $roadmap = Roadmap::create(['user_id' => $user->id, 'title' => 'Laravel Basic', 'graph_data' => []]);
        
        UserRoadmapProgress::create([
            'user_id' => $user->id,
            'roadmap_id' => $roadmap->id,
            'completed_nodes' => ['node_1']
        ]);

        $this->actingAs($user);

        $response = $this->getJson("/progress/{$roadmap->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['completed_nodes' => ['node_1']]);
    }

    /** @test */
    public function test_get_progress_for_unstarted_roadmap()
    {
        $user = User::create(['name' => 'Student', 'email' => 'student@test.com', 'password' => '123']);
        $this->actingAs($user);

        // Прогресса по карте 999 еще нет в БД
        $response = $this->getJson('/progress/999');

        $response->assertStatus(200)
            ->assertJson(['completed_nodes' => []]);
    }
}