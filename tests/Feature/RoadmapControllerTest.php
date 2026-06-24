<?php

namespace Tests\Feature;

use App\Models\Roadmap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadmapControllerTest extends TestCase
{
    use RefreshDatabase; // Очищает базу данных перед каждым тестом

    /**
     * Тест создания Роадмапа.
     */
    public function test_can_create_roadmap()
{
    $user = User::create([
        'name' => 'Author',
        'email' => 'author@example.com',
        'password' => bcrypt('password'),
    ]);

    // ВАЖНО: Симулируем авторизацию этого пользователя в системе!
    $this->actingAs($user); 

    $response = $this->postJson('/roadmaps', [
        'user_id' => $user->id,
        'title'   => 'Новый крутой Роадмап',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('roadmaps', [
        'title'   => 'Новый крутой Роадмап',
        'user_id' => $user->id
    ]);
}

    /**
     * Тест обновления Роадмапа.
     */
    public function test_can_update_roadmap()
    {
        $user = User::create(['name' => 'User', 'email' => 'u1@ex.com', 'password' => '123']);
        
        // Создаем существующий роадмап в базе
        $roadmap = Roadmap::create([
            'user_id'    => $user->id,
            'title'      => 'Старое название',
            'graph_data' => []
        ]);

        // Отправляем PUT запрос на обновление названия
        $response = $this->putJson("/roadmaps/{$roadmap->id}", [
            'title' => 'Обновленное название роадмапа'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('roadmaps', [
            'id'    => $roadmap->id,
            'title' => 'Обновленное название роадмапа'
        ]);
    }

    /**
     * Тест удаления Роадмапа.
     */
    public function test_can_delete_roadmap()
    {
        $user = User::create(['name' => 'User', 'email' => 'u2@ex.com', 'password' => '123']);
        $roadmap = Roadmap::create(['user_id' => $user->id, 'title' => 'На удаление', 'graph_data' => []]);

        // Отправляем DELETE запрос
        $response = $this->deleteJson("/roadmaps/{$roadmap->id}");

        $response->assertStatus(200);
        // Проверяем, что записи больше нет в таблице
        $this->assertDatabaseMissing('roadmaps', ['id' => $roadmap->id]);
    }
}
