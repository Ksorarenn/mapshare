<?php

namespace App\Http\Controllers;

use App\Models\Roadmap;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function __construct()
    {
        // Гарантируем, что только администратор может попасть в контроллер
        $this->middleware(function (Request $request, $next) {
            if (!$request->user() || !$request->user()->isAdmin()) {
                return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
            }
            return $next($request);
        });
    }

    /**
     * Удалить любой роадмап.
     */
    public function destroyRoadmap($id)
    {
        $roadmap = Roadmap::findOrFail($id);
        $roadmap->delete();
        return response()->json(['message' => 'Roadmap deleted'], Response::HTTP_OK);
    }

    /**
     * Получить список всех роадмапов (для админа).
     */
    public function index()
    {
        $roadmaps = Roadmap::orderByDesc('created_at')->paginate(20);
        return response()->json($roadmaps, Response::HTTP_OK);
    }
}
