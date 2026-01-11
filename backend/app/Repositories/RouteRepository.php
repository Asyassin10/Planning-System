<?php

namespace App\Repositories;

use App\Models\Route;
use Illuminate\Database\Eloquent\Collection;

class RouteRepository
{
    public function __construct(
        protected Route $model
    ) {}

    public function all(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    public function findById(int $id): ?Route
    {
        return $this->model->find($id);
    }

    public function create(array $data): Route
    {
        return $this->model->create($data);
    }

    public function update(Route $route, array $data): bool
    {
        return $route->update($data);
    }

    public function delete(Route $route): bool
    {
        return $route->delete();
    }
}
