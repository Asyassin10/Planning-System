<?php

namespace App\Services;

use App\Models\ExecutionEvent;
use App\Repositories\ExecutionEventRepository;
use Illuminate\Database\Eloquent\Collection;

class ExecutionEventService
{
    public function __construct(
        protected ExecutionEventRepository $repository
    ) {}

    public function getAllExecutionEvents(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    public function getExecutionEventById(int $id): ?ExecutionEvent
    {
        return $this->repository->findById($id);
    }

    public function recordEvent(array $data, int $userId): ExecutionEvent
    {
        $executionEvent = $this->repository->create($data, $userId);

        return $executionEvent->load([
            'operationalPlanVersion.operationalPlan.planningRequestItem.route',
            'operationalPlanVersion.operationalPlan.activeVersion.resources.resource',
            'recorder',
        ]);
    }
}
