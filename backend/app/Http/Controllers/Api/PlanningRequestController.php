<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanningRequestRequest;
use App\Http\Requests\UpdatePlanningRequestRequest;
use App\Models\PlanningRequest;
use App\Services\PlanningRequestService;
use Illuminate\Http\JsonResponse;

class PlanningRequestController extends Controller
{
    public function __construct(
        protected PlanningRequestService $service
    ) {}

    public function index(): JsonResponse
    {
        $planningRequests = $this->service->getAllPlanningRequests();

        return response()->json([
            'success' => true,
            'data' => $planningRequests,
        ]);
    }

    public function submitted(): JsonResponse
    {
        $planningRequests = $this->service->getSubmittedRequests();

        return response()->json([
            'success' => true,
            'data' => $planningRequests,
        ]);
    }

    public function draft(): JsonResponse
    {
        $planningRequests = $this->service->getDraftRequests();

        return response()->json([
            'success' => true,
            'data' => $planningRequests,
        ]);
    }

    public function store(StorePlanningRequestRequest $request): JsonResponse
    {
        try {
            $planningRequest = $this->service->createPlanningRequest(
                $request->validated(),
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Planning request created successfully',
                'data' => $planningRequest,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create planning request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(PlanningRequest $planningRequest): JsonResponse
    {
        $planningRequest->load(['creator', 'items.route']);

        return response()->json([
            'success' => true,
            'data' => $planningRequest,
        ]);
    }

    public function update(UpdatePlanningRequestRequest $request, PlanningRequest $planningRequest): JsonResponse
    {
        try {
            $planningRequest = $this->service->updatePlanningRequest(
                $planningRequest,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Planning request updated successfully',
                'data' => $planningRequest,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getMessage() === 'Cannot update a submitted planning request' ? 403 : 500);
        }
    }

    public function destroy(PlanningRequest $planningRequest): JsonResponse
    {
        try {
            $this->service->deletePlanningRequest($planningRequest);

            return response()->json([
                'success' => true,
                'message' => 'Planning request deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    public function submit(PlanningRequest $planningRequest): JsonResponse
    {
        try {
            $planningRequest = $this->service->submitPlanningRequest($planningRequest);

            return response()->json([
                'success' => true,
                'message' => 'Planning request submitted successfully',
                'data' => $planningRequest,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
