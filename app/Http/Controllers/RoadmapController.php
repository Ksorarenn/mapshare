<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoadmapStoreRequest;
use App\Http\Requests\RoadmapUpdateRequest;
use App\Models\Roadmap;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoadmapController extends Controller
{
    /**
     * Список публичных роадмапов с пагинацией и поиском.
     */
    public function index(Request $request)
    {
        $query = Roadmap::query();

        if ($search = $request->query('search')) {
            $query->where('title', 'ilike', "%{$search}%");
        }

        $roadmaps = $query->orderByDesc('created_at')->paginate(15);
        return response()->json($roadmaps, Response::HTTP_OK);
    }

    /**
     * Показать конкретный роадмап с содержимым узлов.
     */
    public function show($id)
    {
        $roadmap = Roadmap::with('nodeContents')->findOrFail($id);
        $roadmap->increment('views_count');
        return response()->json($roadmap, Response::HTTP_OK);
    }

    /**
     * Создать новый роадмап.
     */
    public function store(RoadmapStoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $roadmap = Roadmap::create($data);
        return response()->json($roadmap, Response::HTTP_CREATED);
    }

    /**
     * Обновить существующий роадмап.
     */
    public function update(RoadmapUpdateRequest $request, $id)
    {
        $roadmap = Roadmap::findOrFail($id);
        // проверка прав будет в политике RoadmapPolicy
        $roadmap->update($request->validated());
        return response()->json($roadmap, Response::HTTP_OK);
    }

    /**
     * Удалить роадмап (владелец или админ).
     */
    public function destroy($id)
    {
        $roadmap = Roadmap::findOrFail($id);
        // проверка прав будет в политике RoadmapPolicy
        $roadmap->delete();
        return response()->json(['message' => 'Roadmap deleted'], Response::HTTP_OK);
    }

    /**
     * Список роадмапов текущего пользователя.
     */
    public function userRoadmaps(Request $request)
    {
        $roadmaps = Roadmap::ownedBy($request->user()->id)->orderByDesc('created_at')->get();
        return response()->json($roadmaps, Response::HTTP_OK);
    }
}
