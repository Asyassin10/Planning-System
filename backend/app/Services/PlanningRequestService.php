<?php

namespace App\Services;

use App\Models\PlanningRequest;
use App\Repositories\PlanningRequestRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PlanningRequestService
{
    public function __construct(
        protected PlanningRequestRepository $repository
    ) {}

    public function getAllPlanningRequests(): Collection
    {
        return $this->repository->all();
    }

    public function getSubmittedRequests(): Collection
    {
        return $this->repository->findByStatus('submitted');
    }

    public function getDraftRequests(): Collection
    {
        return $this->repository->findByStatus('draft');
    }

    public function getPlanningRequestById(int $id): ?PlanningRequest
    {
        return $this->repository->findById($id);
    }

    public function createPlanningRequest(array $data, int $userId): PlanningRequest
    {
        return DB::transaction(function () use ($data, $userId) {
            $planningRequest = $this->repository->create($data, $userId);

            foreach ($data['items'] as $item) {
                $this->repository->createItem($planningRequest->id, $item);
            }

            return $planningRequest->load(['items.route', 'creator']);
        });
    }

    public function updatePlanningRequest(PlanningRequest $planningRequest, array $data): PlanningRequest
    {
        if ($planningRequest->isSubmitted()) {
            throw new \Exception('Cannot update a submitted planning request');
        }

        DB::transaction(function () use ($planningRequest, $data) {
            if (isset($data['items'])) {
                $this->repository->deleteItems($planningRequest);

                foreach ($data['items'] as $item) {
                    $this->repository->createItem($planningRequest->id, $item);
                }
            }
        });

        return $planningRequest->load(['items.route', 'creator']);
    }

    public function deletePlanningRequest(PlanningRequest $planningRequest): bool
    {
        if ($planningRequest->isSubmitted()) {
            throw new \Exception('Cannot delete a submitted planning request');
        }

        return $this->repository->delete($planningRequest);
    }

    public function submitPlanningRequest(PlanningRequest $planningRequest): PlanningRequest
    {
        if ($planningRequest->isSubmitted()) {
            throw new \Exception('Planning request is already submitted');
        }

        if ($this->repository->getItemCount($planningRequest) === 0) {
            throw new \Exception('Cannot submit a planning request with no items');
        }

        $this->repository->submit($planningRequest);

        return $planningRequest->load(['items.route', 'creator']);
    }
}
