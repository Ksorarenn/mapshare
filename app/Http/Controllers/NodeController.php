<?php

namespace App\Http\Controllers;

use App\Http\Requests\NodeStoreRequest;
use App\Http\Requests\NodeUpdateRequest;
use App\Models\Roadmap;
use App\Models\NodeContent;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NodeController extends Controller
{
    /**
     * Добавить узел в роадмап.
     */
    public function store(NodeStoreRequest $request, $roadmapId)
    {
        $roadmap = Roadmap::findOrFail($roadmapId);
        // проверка прав – будет в политике RoadmapPolicy

        $data = $request->validated();
        $data['roadmap_id'] = $roadmap->id;
        $node = NodeContent::create($data);

        // Добавляем узел в graph_data (простая структура {id: node_id, type: 'default', position: {x:0,y:0}})
        $graph = $roadmap->graph_data ?? [];
        $graph[] = [
            'id'       => $data['node_id'],
            'type'     => 'default',
            'position' => ['x' => 0, 'y' => 0],
        ];
        $roadmap->graph_data = $graph;
        $roadmap->save();

        return response()->json($node, Response::HTTP_CREATED);
    }

    /**
     * Обновить контент узла.
     */
    public function update(NodeUpdateRequest $request, $roadmapId, $nodeId)
    {
        $roadmap = Roadmap::findOrFail($roadmapId);
        $node = NodeContent::where('roadmap_id', $roadmap->id)
            ->where('node_id', $nodeId)
            ->firstOrFail();

        $node->update($request->validated());
        return response()->json($node, Response::HTTP_OK);
    }

    /**
     * Удалить узел из роадмапа.
     */
    public function destroy($roadmapId, $nodeId)
    {
        $roadmap = Roadmap::findOrFail($roadmapId);
        $node = NodeContent::where('roadmap_id', $roadmap->id)
            ->where('node_id', $nodeId)
            ->firstOrFail();
        $node->delete();

        // Удаляем узел из graph_data
        $graph = $roadmap->graph_data ?? [];
        $graph = array_filter($graph, fn($n) => ($n['id'] ?? null) !== $nodeId);
        $roadmap->graph_data = array_values($graph);
        $roadmap->save();

        return response()->json(['message' => 'Node deleted'], Response::HTTP_OK);
    }
}
