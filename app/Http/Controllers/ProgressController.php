<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgressStoreRequest;
use App\Models\UserRoadmapProgress;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProgressController extends Controller
{
    /**
     * Сохранить/обновить прогресс пользователя.
     */
    public function store(ProgressStoreRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        $progress = UserRoadmapProgress::firstOrNew([
            'user_id'    => $user->id,
            'roadmap_id' => $data['roadmap_id'],
        ]);
        $progress->completed_nodes = $data['completed_nodes'];
        $progress->save();

        return response()->json($progress, Response::HTTP_OK);
    }

    /**
     * Получить прогресс текущего пользователя по конкретному роадмапу.
     */
    public function show($roadmapId, Request $request)
    {
        $progress = UserRoadmapProgress::where('user_id', $request->user()->id)
            ->where('roadmap_id', $roadmapId)
            ->first();

        if (! $progress) {
            return response()->json(['completed_nodes' => []], Response::HTTP_OK);
        }

        return response()->json($progress, Response::HTTP_OK);
    }
}
