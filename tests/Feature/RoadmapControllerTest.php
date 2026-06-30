<?php

namespace Tests\Feature;

use App\Models\Roadmap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadmapControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_create_roadmap_missing_title_validation()
    {
        $user = User::create(['name' => 'Author', 'email' => 'a@test.com', 'password' => '123']);
        $this->actingAs($user);

        $response = $this->postJson('/roadmaps', ['title' => '']);
        $response->assertStatus(422);
    }

    /** @test */
    public function test_create_roadmap_title_length_validation()
    {
        $user = User::create(['name' => 'Author', 'email' => 'a@test.com', 'password' => '123']);
        $this->actingAs($user);

        $response = $this->postJson('/roadmaps', ['title' => str_repeat('a', 256)]);
        $response->assertStatus(422);
    }

    /** @test */
    public function test_successful_roadmap_creation()
    {
        $user = User::create(['name' => 'Author', 'email' => 'a@test.com', 'password' => '123']);
        $this->actingAs($user);

        $response = $this->post('/roadmaps', ['title' => 'My New Roadmap']);
        
        // Контроллер выполняет Inertia-редирект на страницу списка личных карт
        $response->assertRedirect(route('my-roadmaps'));
        $this->assertDatabaseHas('roadmaps', ['title' => 'My New Roadmap', 'user_id' => $user->id]);
    }

    /** @test */
    public function test_get_public_roadmaps_list()
    {
        $user = User::create(['name' => 'Author', 'email' => 'a@test.com', 'password' => '123']);
        Roadmap::create(['user_id' => $user->id, 'title' => 'Roadmap 1', 'graph_data' => []]);

        $response = $this->getJson('/roadmaps');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'current_page', 'total']);
    }

    /** @test */
    public function test_search_roadmaps_by_title()
    {
        $user = User::create(['name' => 'Author', 'email' => 'a@test.com', 'password' => '123']);
        Roadmap::create(['user_id' => $user->id, 'title' => 'Laravel Basic', 'graph_data' => []]);
        Roadmap::create(['user_id' => $user->id, 'title' => 'Vue JS Guide', 'graph_data' => []]);

        $response = $this->getJson('/roadmaps?search=laravel');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function test_view_specific_roadmap()
    {
        $user = User::create(['name' => 'Author', 'email' => 'a@test.com', 'password' => '123']);
        $roadmap = Roadmap::create(['user_id' => $user->id, 'title' => 'View Me', 'graph_data' => []]);

        $response = $this->get("/roadmaps/{$roadmap->id}");
        $response->assertStatus(200); // Одобряет Inertia::render
    }

    /** @test */
    public function test_cannot_update_others_roadmap()
    {
        $userA = User::create(['name' => 'User A', 'email' => 'a@test.com', 'password' => '123']);
        $userB = User::create(['name' => 'User B', 'email' => 'b@test.com', 'password' => '123']);
        
        $roadmapB = Roadmap::create(['user_id' => $userB->id, 'title' => 'Roadmap B', 'graph_data' => []]);

        $this->actingAs($userA);
        $response = $this->patchJson("/roadmaps/{$roadmapB->id}", ['title' => 'Hacked Title']);
        
        $response->assertStatus(403);
    }

    /** @test */
    public function test_owner_can_update_roadmap()
    {
        $user = User::create(['name' => 'Owner', 'email' => 'o@test.com', 'password' => '123']);
        $roadmap = Roadmap::create(['user_id' => $user->id, 'title' => 'Old Title', 'graph_data' => []]);

        $this->actingAs($user);
        $response = $this->patch("/roadmaps/{$roadmap->id}", ['title' => 'Updated Title']);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('roadmaps', ['id' => $roadmap->id, 'title' => 'Updated Title']);
    }

    /** @test */
    public function test_owner_can_delete_roadmap()
    {
        $user = User::create(['name' => 'Owner', 'email' => 'o@test.com', 'password' => '123']);
        $roadmap = Roadmap::create(['user_id' => $user->id, 'title' => 'To Delete', 'graph_data' => []]);

        $this->actingAs($user);
        $response = $this->delete("/roadmaps/{$roadmap->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('roadmaps', ['id' => $roadmap->id]);
    }

    /** @test */
    public function test_view_user_own_roadmaps()
    {
        $user = User::create(['name' => 'Owner', 'email' => 'o@test.com', 'password' => '123']);
        $this->actingAs($user);

        $response = $this->get('/my-roadmaps');
        $response->assertStatus(200);
    }
}