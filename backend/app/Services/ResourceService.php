<?php

namespace App\Services;

use App\Models\Resource;
use App\Repositories\ResourceRepository;
use Illuminate\Database\Eloquent\Collection;

class ResourceService
{
    public function __construct(
        protected ResourceRepository $repository
    ) {}

    public function getAllResources(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    public function getResourceById(int $id): ?Resource
    {
        return $this->repository->findById($id);
    }

    public function createResource(array $data): Resource
    {
        return $this->repository->create($data);
    }

    public function updateResource(Resource $resource, array $data): Resource
    {
        $this->repository->update($resource, $data);
        return $resource->fresh();
    }

    public function deleteResource(Resource $resource): bool
    {
        return $this->repository->delete($resource);
    }
}
