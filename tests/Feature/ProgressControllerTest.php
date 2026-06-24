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

    /**
     * Тест обновления прогресса пользователя.
     */
    public function test_can_update_user_roadmap_progress()
    {
         // 1. Создаем пользователя и роадмап
        $user = User::create(['name' => 'Student', 'email' => 'student@ex.com', 'password' => '123']);
        $roadmap = Roadmap::create(['user_id' => $user->id, 'title' => 'Laravel Basic', 'graph_data' => []]);

        // Авторизуем пользователя (так как в контроллере есть $request->user())
        $this->actingAs($user); 

        // 2. Отправляем запрос в метод store (у вас один метод на сохранение и обновление)
        // Внимание: проверьте URL. Если роут ведет на store, обычно это POST-запрос
        $response = $this->postJson("/progress", [
            'roadmap_id'      => $roadmap->id,
            'completed_nodes' => ['node_123'], // Передаем массив узлов
        ]);

        // 3. Проверяем результаты
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('user_roadmap_progress', [
            'user_id'    => $user->id,
            'roadmap_id' => $roadmap->id,
            // Проверяем completed_nodes. Laravel автоматически превратит массив в JSON для БД
            'completed_nodes' => json_encode(['node_123']), 
        ]);
    }
}