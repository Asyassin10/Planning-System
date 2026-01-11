<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResourceRequest;
use App\Http\Requests\UpdateResourceRequest;
use App\Models\Resource;
use App\Services\ResourceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function __construct(
        protected ResourceService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = [
            'type' => $request->input('type'),
            'active_only' => $request->boolean('active_only'),
        ];

        $resources = $this->service->getAllResources($filters);

        return response()->json([
            'success' => true,
            'data' => $resources,
        ]);
    }

    public function store(StoreResourceRequest $request): JsonResponse
    {
        $resource = $this->service->createResource($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Resource created successfully',
            'data' => $resource,
        ], 201);
    }

    public function show(Resource $resource): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $resource,
        ]);
    }

    public function update(UpdateResourceRequest $request, Resource $resource): JsonResponse
    {
        $resource = $this->service->updateResource($resource, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Resource updated successfully',
            'data' => $resource,
        ]);
    }

    public function destroy(Resource $resource): JsonResponse
    {
        $this->service->deleteResource($resource);

        return response()->json([
            'success' => true,
            'message' => 'Resource deleted successfully',
        ]);
    }
}