<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVersionRequest;
use App\Http\Requests\StoreOperationalPlanRequest;
use App\Models\OperationalPlan;
use App\Models\OperationalPlanVersion;
use App\Services\OperationalPlanService;
use Illuminate\Http\JsonResponse;

class OperationalPlanController extends Controller
{
    public function __construct(
        protected OperationalPlanService $service
    ) {}

    public function index(): JsonResponse
    {
        $operationalPlans = $this->service->getAllOperationalPlans();

        return response()->json([
            'success' => true,
            'data' => $operationalPlans,
        ]);
    }

    public function store(StoreOperationalPlanRequest $request): JsonResponse
    {
        try {
            $operationalPlan = $this->service->createOperationalPlan(
                $request->validated(),
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Operational plan created successfully',
                'data' => $operationalPlan,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create operational plan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(OperationalPlan $operationalPlan): JsonResponse
    {
        $operationalPlan->load([
            'planningRequestItem.route',
            'planningRequestItem.planningRequest',
            'creator',
            'versions.resources.resource',
            'versions.creator',
        ]);

        return response()->json([
            'success' => true,
            'data' => $operationalPlan,
        ]);
    }

    public function createVersion(CreateVersionRequest $request, OperationalPlan $operationalPlan): JsonResponse
    {
        try {
            $version = $this->service->createVersion(
                $operationalPlan,
                $request->validated(),
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Version created successfully',
                'data' => $version,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create version',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function activateVersion(OperationalPlanVersion $version): JsonResponse
    {
        try {
            $version = $this->service->activateVersion($version);

            return response()->json([
                'success' => true,
                'message' => 'Version activated successfully',
                'data' => $version,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate version',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getActivePlans(): JsonResponse
    {
        $activePlans = $this->service->getActivePlans();

        return response()->json([
            'success' => true,
            'data' => $activePlans,
        ]);
    }
}
