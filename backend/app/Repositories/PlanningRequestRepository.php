<?php

namespace App\Repositories;

use App\Models\PlanningRequest;
use App\Models\PlanningRequestItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PlanningRequestRepository
{
    public function __construct(
        protected PlanningRequest $model,
        protected PlanningRequestItem $itemModel
    ) {}

    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function all(): Collection
    {
        return $this->query()
            ->with(['creator', 'items.route'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByStatus(string $status): Collection
    {
        return $this->query()
            ->with(['creator', 'items.route'])
            ->where('status', $status)
            ->orderBy($status === 'submitted' ? 'submitted_at' : 'created_at', 'desc')
            ->get();
    }

    public function findById(int $id): ?PlanningRequest
    {
        return $this->model->find($id);
    }

    public function create(array $data, int $userId): PlanningRequest
    {
        return $this->model->create([
            'created_by' => $userId,
            'status' => 'draft',
        ]);
    }

    public function createItem(int $planningRequestId, array $itemData): PlanningRequestItem
    {
        return $this->itemModel->create([
            'planning_request_id' => $planningRequestId,
            'route_id' => $itemData['route_id'],
            'capacity' => $itemData['capacity'],
            'start_date' => $itemData['start_date'],
            'end_date' => $itemData['end_date'],
        ]);
    }

    public function deleteItems(PlanningRequest $planningRequest): void
    {
        $planningRequest->items()->delete();
    }

    public function delete(PlanningRequest $planningRequest): bool
    {
        return $planningRequest->delete();
    }

    public function submit(PlanningRequest $planningRequest): bool
    {
        return $planningRequest->submit();
    }

    public function getItemCount(PlanningRequest $planningRequest): int
    {
        return $planningRequest->items()->count();
    }
}
