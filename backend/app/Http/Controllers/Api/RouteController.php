<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRouteRequest;
use App\Http\Requests\UpdateRouteRequest;
use App\Models\Route;
use App\Services\RouteService;
use Illuminate\Http\JsonResponse;

class RouteController extends Controller
{
    public function __construct(
        protected RouteService $service
    ) {}

    public function index(): JsonResponse
    {
        $routes = $this->service->getAllRoutes();

        return response()->json([
            'success' => true,
            'data' => $routes,
        ]);
    }

    public function store(StoreRouteRequest $request): JsonResponse
    {
        $route = $this->service->createRoute($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Route created successfully',
            'data' => $route,
        ], 201);
    }

    public function show(Route $route): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $route,
        ]);
    }

    public function update(UpdateRouteRequest $request, Route $route): JsonResponse
    {
        $route = $this->service->updateRoute($route, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Route updated successfully',
            'data' => $route,
        ]);
    }

    public function destroy(Route $route): JsonResponse
    {
        $this->service->deleteRoute($route);

        return response()->json([
            'success' => true,
            'message' => 'Route deleted successfully',
        ]);
    }
}
