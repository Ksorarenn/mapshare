<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NodeController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\RoadmapController;
use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
*/

// Public authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Public roadmaps list (no auth required)
Route::get('/roadmaps', [RoadmapController::class, 'index']);
Route::get('/roadmaps/{id}', [RoadmapController::class, 'show']);

// Routes that require authentication (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user',   [AuthController::class, 'user']);


// Authenticated routes
    // Roadmaps management for authenticated users
    Route::post('/roadmaps', [RoadmapController::class, 'store']);
    Route::patch('/roadmaps/{id}', [RoadmapController::class, 'update']);
    //Route::delete('/roadmaps/{id}', [RoadmapController::class, 'destroy']);
    Route::get('/user/roadmaps', [RoadmapController::class, 'userRoadmaps'])->name('user.roadmaps');

    // Nodes management
    Route::post('/roadmaps/{roadmapId}/nodes', [NodeController::class, 'store']);
    Route::patch('/roadmaps/{roadmapId}/nodes/{nodeId}', [NodeController::class, 'update']);
    Route::delete('/roadmaps/{roadmapId}/nodes/{nodeId}', [NodeController::class, 'destroy']);

    // Progress tracking
    Route::post('/progress', [ProgressController::class, 'store']);
    Route::get('/progress/{roadmapId}', [ProgressController::class, 'show']);

    // Admin routes – только для администраторов (проверка внутри контроллера)
    Route::delete('/admin/roadmaps/{id}', [AdminController::class, 'destroyRoadmap']);
    Route::get('/admin/roadmaps', [AdminController::class, 'index']);
});
