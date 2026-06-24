<?php

namespace Tests\Feature;

use App\Models\Roadmap;
use App\Models\NodeContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NodeControllerTest extends TestCase
{
    // Очищает базу данных после каждого теста (используйте тестовую БД!)
    use RefreshDatabase;

    public function test_can_create_node()
    {
        // 1. Создаем пользователя (если у вас стандартная модель User)
        // Если модели User еще нет, временно напишите 'user_id' => 1 в шаге 2
        $user = \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // 2. Создаем Роадмап, привязанный к пользователю
        $roadmap = Roadmap::create([
            'user_id' => $user->id, // Передаем обязательное поле user_id
            'title' => 'Test Roadmap',
            'graph_data' => []
        ]);

        // 3. Отправляем запрос в контроллер
        $response = $this->postJson("/roadmaps/{$roadmap->id}/nodes", [
    'node_id' => 'node_999',
    'content' => 'Тестовый контент',
    'links'   => ['https://google.com'], // Передаем как чистый массив
    'image_path' => null
]);

        // 4. Проверяем, что сервер ответил "Создано" (201 HTTP CREATED)
        // Внимание: в прошлом примере кода была опечатка (21), исправьте на 201
        $response->assertStatus(201); 
        $this->assertDatabaseHas('node_contents', ['node_id' => 'node_999']);
    }
}
