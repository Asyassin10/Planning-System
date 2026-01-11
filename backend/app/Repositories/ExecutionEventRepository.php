<?php

namespace App\Repositories;

use App\Models\ExecutionEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ExecutionEventRepository
{
    public function __construct(
        protected ExecutionEvent $model
    ) {}

    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function all(array $filters = []): Collection
    {
        $query = $this->query()->with([
            'operationalPlanVersion.operationalPlan.planningRequestItem.route',
            'operationalPlanVersion.operationalPlan.activeVersion.resources.resource',
            'recorder',
        ]);

        if (isset($filters['operational_plan_version_id'])) {
            $query->where('operational_plan_version_id', $filters['operational_plan_version_id']);
        }

        if (isset($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }

        return $query->orderBy('recorded_at', 'desc')->get();
    }

    public function findById(int $id): ?ExecutionEvent
    {
        return $this->model->find($id);
    }

    public function create(array $data, int $userId): ExecutionEvent
    {
        return $this->model->create([
            'operational_plan_version_id' => $data['operational_plan_version_id'],
            'event_type' => $data['event_type'],
            'event_data' => $data['event_data'] ?? null,
            'recorded_by' => $userId,
            'recorded_at' => $data['recorded_at'] ?? now(),
        ]);
    }
}