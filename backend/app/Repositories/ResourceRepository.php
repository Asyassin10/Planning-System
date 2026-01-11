<?php

namespace App\Repositories;

use App\Models\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ResourceRepository
{
    public function __construct(
        protected Resource $model
    ) {}

    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function all(array $filters = []): Collection
    {
        $query = $this->query();

        if (isset($filters['type'])) {
            $query->ofType($filters['type']);
        }

        if (isset($filters['active_only']) && $filters['active_only']) {
            $query->active();
        }

        return $query->orderBy('name')->get();
    }

    public function findById(int $id): ?Resource
    {
        return $this->model->find($id);
    }

    public function create(array $data): Resource
    {
        return $this->model->create($data);
    }

    public function update(Resource $resource, array $data): bool
    {
        return $resource->update($data);
    }

    public function delete(Resource $resource): bool
    {
        return $resource->delete();
    }
}
