<?php

use App\Http\Controllers\NodeController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\RoadmapController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // При открытии корня проверяем авторизацию
    return auth()->check() ? redirect()->route('gallery') : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');//->middleware(['auth', 'verified'])

// Gallery page (public, можно открыть без auth)
Route::get('/gallery', [\App\Http\Controllers\PageController::class, 'Gallery'])->name('gallery');

Route::middleware('auth')->group(function () {
    Route::get('/roadmaps/create', fn() => Inertia::render('RoadmapEditor'))->name('roadmaps.create');
    Route::get('/my-roadmaps', fn() => Inertia::render('MyRoadmaps'))->name('my-roadmaps');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// Регистрируем маршруты для нашего NodeController
Route::post('/roadmaps/{roadmapId}/nodes', [NodeController::class, 'store']);
Route::put('/roadmaps/{roadmapId}/nodes/{nodeId}', [NodeController::class, 'update']);
Route::delete('/roadmaps/{roadmapId}/nodes/{nodeId}', [NodeController::class, 'destroy']);

// --- Роуты для RoadmapController (public GET)
Route::get('/roadmaps', [RoadmapController::class, 'index']);
Route::get('/roadmaps/{id}', [RoadmapController::class, 'show']);

// --- Роуты для RoadmapController (модификация, требует auth)
Route::post('/roadmaps', [RoadmapController::class, 'store']);             // Создание роадмапа (метод store)
Route::put('/roadmaps/{id}', [RoadmapController::class, 'update']);        // Обновление (метод update)
Route::delete('/roadmaps/{id}', [RoadmapController::class, 'destroy']);    // Удаление (метод destroy)


// --- Роут для ProgressController 
// Внимание: проверьте, как в вашем контроллере называется метод обновления прогресса 
// Если метод называется 'update', оставьте 'update'. Если по-другому (например, 'updateProgress'), замените ниже.
Route::post('/progress', [ProgressController::class, 'store']);
Route::get('/progress/{roadmapId}', [ProgressController::class, 'show']);
// Allow preflight OPTIONS for any path (CORS)
Route::options('{any}', function(){ return response()->noContent(); })->where('any', '.*');

require __DIR__.'/auth.php';
