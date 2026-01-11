<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExecutionEventController;
use App\Http\Controllers\Api\OperationalPlanController;
use App\Http\Controllers\Api\PlanningRequestController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\RouteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public authentication routes
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

// Public/shared routes
Route::apiResource('routes', RouteController::class);
Route::apiResource('resources', ResourceController::class);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {

    // Auth routes
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/user', [AuthController::class, 'user']);

    // Planning Requests (Team A)
    Route::get('planning-requests/submitted', [PlanningRequestController::class, 'submitted']);
    Route::get('planning-requests/draft', [PlanningRequestController::class, 'draft']);
    Route::apiResource('planning-requests', PlanningRequestController::class);
    Route::post('planning-requests/{planningRequest}/submit', [PlanningRequestController::class, 'submit']);

    // Operational Plans (Team B)
    Route::apiResource('operational-plans', OperationalPlanController::class)->only(['index', 'show', 'store']);
    Route::get('operational-plans/active', [OperationalPlanController::class, 'getActivePlans']);
    Route::post('operational-plans/{operationalPlan}/versions', [OperationalPlanController::class, 'createVersion']);
    Route::post('operational-plan-versions/{version}/activate', [OperationalPlanController::class, 'activateVersion']);

    // Execution Events (Team C)
    Route::apiResource('execution-events', ExecutionEventController::class)->only(['index', 'show', 'store']);
});
