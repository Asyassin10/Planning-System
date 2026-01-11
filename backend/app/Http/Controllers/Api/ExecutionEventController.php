<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExecutionEventRequest;
use App\Models\ExecutionEvent;
use App\Services\ExecutionEventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExecutionEventController extends Controller
{
    public function __construct(
        protected ExecutionEventService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = [
            'operational_plan_version_id' => $request->input('operational_plan_version_id'),
            'event_type' => $request->input('event_type'),
        ];

        $executionEvents = $this->service->getAllExecutionEvents($filters);

        return response()->json([
            'success' => true,
            'data' => $executionEvents,
        ]);
    }

    public function store(StoreExecutionEventRequest $request): JsonResponse
    {
        $executionEvent = $this->service->recordEvent(
            $request->validated(),
            $request->user()->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Execution event recorded successfully',
            'data' => $executionEvent,
        ], 201);
    }

    public function show(ExecutionEvent $executionEvent): JsonResponse
    {
        $executionEvent->load([
            'operationalPlanVersion.operationalPlan.planningRequestItem.route',
            'operationalPlanVersion.operationalPlan.activeVersion.resources.resource',
            'recorder',
        ]);

        return response()->json([
            'success' => true,
            'data' => $executionEvent,
        ]);
    }
}