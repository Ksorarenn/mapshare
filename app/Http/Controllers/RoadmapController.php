<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoadmapStoreRequest;
use App\Http\Requests\RoadmapUpdateRequest;
use App\Models\Roadmap;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

class RoadmapController extends Controller
{
    use AuthorizesRequests;
    /**
     * Список публичных роадмапов с пагинацией и поиском.
     */
    public function index(Request $request)
{
    $query = Roadmap::query();

    if ($search = $request->query('search')) {
        // Приводим и поле, и строку поиска к нижнему регистру через lower()
        $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($search) . '%']);
    }

    $roadmaps = $query->orderByDesc('created_at')->paginate(15);
    return response()->json($roadmaps, Response::HTTP_OK);
}

    /**
     * Показать конкретный роадмап с содержимым узлов.
     */
    public function show($id)
{
    // Загружаем карту из базы
    $roadmap = Roadmap::findOrFail($id);

    // Рендерим твой Vue компонент (например, RoadmapEditor или RoadmapView) 
    // и прокидываем туда сохраненные данные graph_data и title
    return Inertia::render('RoadmapEditor', [
        'roadmap' => $roadmap
    ]);
}

    /**
     * Создать новый роадмап.
     */
    public function store(RoadmapStoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        
        $roadmap = Roadmap::create($data);
        
        // ФИКС: Вместо json ответа делаем редирект Inertia на страницу списка личных карт
        return redirect()->route('my-roadmaps');
    }

    public function update(RoadmapUpdateRequest $request, $id)
    {
        $roadmap = Roadmap::findOrFail($id);
        
        // КРИТИЧЕСКИЙ ФИКС: Laravel выбросит 403 ошибку, если юзер не владелец
        $this->authorize('update', $roadmap); 

        $roadmap->update($request->validated());
        return redirect()->back();
    }

    /**
 * Удалить роадмап (владелец или админ).
 */
public function destroy($id)
{
    $roadmap = Roadmap::findOrFail($id);

    // Вытаскиваем залогиненного пользователя
    $user = auth()->user();

    // Защита: Удалить карту может только создатель ЛИБО администратор
    if ($user->id !== $roadmap->user_id && !$user->role === 'admin') {
        abort(403, 'У вас нет прав на удаление этого роадмапа.');
    }

    $roadmap->delete();

    // ФИКС РЕДИРЕКТА ДЛЯ INERTIA:
    // Вместо возврата json (response()->json) делаем возврат назад,
    // иначе Inertia-страница сломается после удаления!
    return redirect()->back();
}

    /**
     * Список роадмапов текущего пользователя.
     */
    public function userRoadmaps(Request $request)
    {
        $roadmaps = Roadmap::ownedBy($request->user()->id)->orderByDesc('created_at')->get();
    
    // Возвращаем СТРАНИЦУ Inertia, а не JSON!
    return Inertia::render('MyRoadmaps', [
        'roadmaps' => $roadmaps
    ]);
    }
}
