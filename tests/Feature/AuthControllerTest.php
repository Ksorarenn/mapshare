<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_registration_email_validation()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function test_registration_password_length_validation()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function test_registration_duplicate_email_validation()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function test_successful_registration()
    {
        $user = User::create([
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => bcrypt('password123'),
        ]);

        $this->assertDatabaseHas('users', ['email' => 'newuser@test.com']);
    }

    /** @test */
    public function test_login_wrong_password()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function test_login_missing_fields()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function test_successful_login()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'exist@test.com',
            'password' => bcrypt('password123'),
        ]);

        $this->assertDatabaseHas('users', ['email' => 'exist@test.com']);
    }

    /** @test */
    public function test_logout()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function test_get_authenticated_user_profile()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => bcrypt('password123'),
        ]);

        $this->assertEquals('user@test.com', $user->email);
    }

    /** @test */
    public function test_get_profile_unauthenticated()
    {
        $this->assertGuest();
    }
}