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

// --- Роуты для страниц и управления роадмапами (Требуют авторизации) ---
Route::middleware('auth')->group(function () {
    Route::get('/roadmaps/create', fn() => Inertia::render('RoadmapEditor'))->name('roadmaps.create');
    
    // ФИКС СПИСКА КАРТ: Регистрируем веб-маршрут для вывода личных карт через Inertia
    Route::get('/user/roadmaps', [RoadmapController::class, 'userRoadmaps'])->name('user.roadmaps');
    Route::get('/my-roadmaps', [RoadmapController::class, 'userRoadmaps'])->name('my-roadmaps');
    
    // Модификация карт (Inertia/Веб контекст)
    Route::post('/roadmaps', [RoadmapController::class, 'store'])->name('roadmaps.store');
    
    // ФИКС РЕДИРЕКТА: Меняем PUT на PATCH, чтобы он идеально совпадал с axios.patch на фронтенде
    Route::patch('/roadmaps/{id}', [RoadmapController::class, 'update'])->name('roadmaps.update');
    Route::delete('/roadmaps/{id}', [RoadmapController::class, 'destroy'])->name('roadmaps.destroy');
    

    // Профиль
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Публичные роуты (Доступны без auth)
Route::get('/roadmaps', [RoadmapController::class, 'index'])->name('roadmaps.index');
Route::get('/roadmaps/{id}', [RoadmapController::class, 'show'])->name('roadmaps.show');

// Регистрируем маршруты для нашего NodeController
Route::post('/roadmaps/{roadmapId}/nodes', [NodeController::class, 'store']);
Route::put('/roadmaps/{roadmapId}/nodes/{nodeId}', [NodeController::class, 'update']);
Route::delete('/roadmaps/{roadmapId}/nodes/{nodeId}', [NodeController::class, 'destroy']);

Route::post('/progress', [ProgressController::class, 'store']);
Route::get('/progress/{roadmapId}', [ProgressController::class, 'show']);

// CORS
Route::options('{any}', function(){ return response()->noContent(); })->where('any', '.*');

require __DIR__.'/auth.php';
