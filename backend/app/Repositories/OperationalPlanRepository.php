<?php

namespace App\Repositories;

use App\Models\OperationalPlan;
use App\Models\OperationalPlanVersion;
use App\Models\PlanVersionResource;
use Illuminate\Database\Eloquent\Collection;

class OperationalPlanRepository
{
    public function __construct(
        protected OperationalPlan $model,
        protected OperationalPlanVersion $versionModel,
        protected PlanVersionResource $resourceModel
    ) {}

    public function all(): Collection
    {
        return $this->model->with([
            'planningRequestItem.route',
            'planningRequestItem.planningRequest',
            'creator',
            'versions.resources.resource',
            'activeVersion.resources.resource',
        ])->orderBy('created_at', 'desc')->get();
    }

    public function getActivePlans(): Collection
    {
        return $this->model->with([
            'planningRequestItem.route',
            'planningRequestItem.planningRequest',
            'creator',
            'activeVersion.resources.resource',
        ])->whereHas('activeVersion')->get();
    }

    public function findById(int $id): ?OperationalPlan
    {
        return $this->model->find($id);
    }

    public function create(int $planningRequestItemId, int $userId): OperationalPlan
    {
        return $this->model->create([
            'planning_request_item_id' => $planningRequestItemId,
            'created_by' => $userId,
        ]);
    }

    public function createVersion(int $operationalPlanId, array $data, int $userId): OperationalPlanVersion
    {
        return $this->versionModel->create([
            'operational_plan_id' => $operationalPlanId,
            'version' => $data['version'],
            'is_active' => $data['is_active'],
            'valid_from' => $data['valid_from'],
            'valid_to' => $data['valid_to'],
            'notes' => $data['notes'] ?? null,
            'created_by' => $userId,
        ]);
    }

    public function createVersionResource(int $versionId, array $resourceData): PlanVersionResource
    {
        return $this->resourceModel->create([
            'operational_plan_version_id' => $versionId,
            'resource_id' => $resourceData['resource_id'],
            'capacity' => $resourceData['capacity'],
            'is_permanent' => $resourceData['is_permanent'] ?? true,
            'notes' => $resourceData['notes'] ?? null,
        ]);
    }

    public function getMaxVersion(OperationalPlan $operationalPlan): int
    {
        return $operationalPlan->versions()->max('version') ?? 0;
    }

    public function deactivateVersions(OperationalPlan $operationalPlan): void
    {
        $operationalPlan->versions()->update(['is_active' => false]);
    }

    public function activateVersion(OperationalPlanVersion $version): void
    {
        $version->activate();
    }
}
