<?php

namespace App\Services;

use App\Models\Route;
use App\Repositories\RouteRepository;
use Illuminate\Database\Eloquent\Collection;

class RouteService
{
    public function __construct(
        protected RouteRepository $repository
    ) {}

    public function getAllRoutes(): Collection
    {
        return $this->repository->all();
    }

    public function getRouteById(int $id): ?Route
    {
        return $this->repository->findById($id);
    }

    public function createRoute(array $data): Route
    {
        return $this->repository->create($data);
    }

    public function updateRoute(Route $route, array $data): Route
    {
        $this->repository->update($route, $data);
        return $route->fresh();
    }

    public function deleteRoute(Route $route): bool
    {
        return $this->repository->delete($route);
    }
}
