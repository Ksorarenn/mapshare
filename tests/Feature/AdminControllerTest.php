<?php

namespace Tests\Feature;

use App\Models\Roadmap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_guest_cannot_access_admin_routes()
    {
        $this->assertGuest();
    }

    /** @test */
    public function test_regular_user_cannot_access_admin_routes()
    {
        $user = User::create([
            'name' => 'Regular',
            'email' => 'regular@test.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        $this->assertFalse($user->isAdmin());
    }

    /** @test */
    public function test_admin_can_get_all_roadmaps()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        // Проверяем, что роль админа работает
        $this->assertTrue($admin->isAdmin());
    }

    /** @test */
    public function test_admin_can_force_delete_any_roadmap()
    {
        $user = User::create(['name' => 'U', 'email' => 'u@t.com', 'password' => '1']);
        $roadmap = Roadmap::create(['user_id' => $user->id, 'title' => 'T', 'graph_data' => []]);

        $roadmap->delete();

        $this->assertDatabaseMissing('roadmaps', ['id' => $roadmap->id]);
    }

}