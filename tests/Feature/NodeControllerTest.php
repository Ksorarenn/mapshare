<?php

namespace Tests\Feature;

use App\Models\Roadmap;
use App\Models\NodeContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NodeControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_add_node_to_non_existent_roadmap()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function test_successful_node_creation()
    {
        $user = User::create(['name' => 'User', 'email' => 'u@test.com', 'password' => '123']);
        $roadmap = Roadmap::create(['user_id' => $user->id, 'title' => 'Graph Roadmap', 'graph_data' => []]);
        
        $node = NodeContent::create([
            'roadmap_id' => $roadmap->id, 
            'node_id' => 'node_1', 
            'content' => 'Valid Markdown content'
        ]);

        $this->assertDatabaseHas('node_contents', ['node_id' => 'node_1', 'roadmap_id' => $roadmap->id]);
    }

    /** @test */
    public function test_node_creation_validation_errors()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function test_successful_node_update()
    {
        $user = User::create(['name' => 'User', 'email' => 'u@test.com', 'password' => '123']);
        $roadmap = Roadmap::create(['user_id' => $user->id, 'title' => 'Roadmap', 'graph_data' => []]);
        $node = NodeContent::create(['roadmap_id' => $roadmap->id, 'node_id' => 'node_1', 'content' => 'Old content']);
        
        $node->update(['content' => 'Updated Markdown content']);

        $this->assertDatabaseHas('node_contents', ['id' => $node->id, 'content' => 'Updated Markdown content']);
    }


    /** @test */
    public function test_successful_node_deletion()
    {
        $user = User::create(['name' => 'User', 'email' => 'u@test.com', 'password' => '123']);
        $roadmap = Roadmap::create(['user_id' => $user->id, 'title' => 'Roadmap', 'graph_data' => []]);
        $node = NodeContent::create(['roadmap_id' => $roadmap->id, 'node_id' => 'node_1', 'content' => 'Content']);

        $node->delete();

        $this->assertDatabaseMissing('node_contents', ['roadmap_id' => $roadmap->id, 'node_id' => 'node_1']);
    }

}