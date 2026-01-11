<?php

namespace App\Services;

use App\Models\OperationalPlan;
use App\Models\OperationalPlanVersion;
use App\Repositories\OperationalPlanRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OperationalPlanService
{
    public function __construct(
        protected OperationalPlanRepository $repository
    ) {}

    public function getAllOperationalPlans(): Collection
    {
        return $this->repository->all();
    }

    public function getActivePlans(): Collection
    {
        return $this->repository->getActivePlans();
    }

    public function getOperationalPlanById(int $id): ?OperationalPlan
    {
        return $this->repository->findById($id);
    }

    public function createOperationalPlan(array $data, int $userId): OperationalPlan
    {
        return DB::transaction(function () use ($data, $userId) {
            $operationalPlan = $this->repository->create(
                $data['planning_request_item_id'],
                $userId
            );

            $version = $this->repository->createVersion(
                $operationalPlan->id,
                [
                    'version' => 1,
                    'is_active' => true,
                    'valid_from' => $data['version']['valid_from'],
                    'valid_to' => $data['version']['valid_to'],
                    'notes' => $data['version']['notes'] ?? null,
                ],
                $userId
            );

            if (isset($data['version']['resources'])) {
                foreach ($data['version']['resources'] as $resource) {
                    $this->repository->createVersionResource($version->id, $resource);
                }
            }

            return $operationalPlan->load([
                'planningRequestItem.route',
                'creator',
                'activeVersion.resources.resource',
            ]);
        });
    }

    public function createVersion(OperationalPlan $operationalPlan, array $data, int $userId): OperationalPlanVersion
    {
        return DB::transaction(function () use ($operationalPlan, $data, $userId) {
            $nextVersion = $this->repository->getMaxVersion($operationalPlan) + 1;
            $isActive = $data['is_active'] ?? false;

            if ($isActive) {
                $this->repository->deactivateVersions($operationalPlan);
            }

            $version = $this->repository->createVersion(
                $operationalPlan->id,
                [
                    'version' => $nextVersion,
                    'is_active' => $isActive,
                    'valid_from' => $data['valid_from'],
                    'valid_to' => $data['valid_to'],
                    'notes' => $data['notes'] ?? null,
                ],
                $userId
            );

            if (isset($data['resources'])) {
                foreach ($data['resources'] as $resource) {
                    $this->repository->createVersionResource($version->id, $resource);
                }
            }

            return $version->load(['resources.resource', 'creator']);
        });
    }

    public function activateVersion(OperationalPlanVersion $version): OperationalPlanVersion
    {
        $this->repository->activateVersion($version);
        return $version->load(['resources.resource', 'creator']);
    }
}
