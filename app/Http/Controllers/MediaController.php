<?php

namespace App\Http\Controllers;

use App\Http\Requests\MediaStoreRequest;
use App\Models\NodeContent;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Загрузить изображение для узла.
     *
     * Требования:
     *  – размер ≤ 5 МБ (правило в MediaStoreRequest)
     *  – типы JPEG, PNG, WEBP (правило в MediaStoreRequest)
     */
    public function store(MediaStoreRequest $request, $roadmapId)
    {
        // Проверка прав – будет через политику RoadmapPolicy (или отдельную)
        $roadmap = \App\Models\Roadmap::findOrFail($roadmapId);

        $validated = $request->validated();
        $nodeId   = $validated['node_id'];
        $file     = $request->file('media');

        // Сохраняем файл в публичный диск (storage/app/public/roadmap_media)
        $path = $file->store('roadmap_media', 'public');

        // Находим запись NodeContent и сохраняем путь к изображению
        $nodeContent = NodeContent::where('roadmap_id', $roadmap->id)
            ->where('node_id', $nodeId)
            ->firstOrFail();
        $nodeContent->image_path = $path;
        $nodeContent->save();

        return response()->json([
            'message' => 'Media uploaded',
            'path'    => Storage::url($path),
        ], Response::HTTP_CREATED);
    }
}
