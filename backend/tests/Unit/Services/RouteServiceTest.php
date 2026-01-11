<?php

namespace Tests\Unit\Services;

use App\Models\Route;
use App\Repositories\RouteRepository;
use App\Services\RouteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class RouteServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(RouteRepository::class);
        $this->service = new RouteService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_routes()
    {
        $this->repository->shouldReceive('all')
            ->once()
            ->andReturn(collect([]));

        $result = $this->service->getAllRoutes();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_route_by_id()
    {
        $route = Mockery::mock(Route::class);

        $this->repository->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($route);

        $result = $this->service->getRouteById(1);

        $this->assertInstanceOf(Route::class, $result);
    }

    public function test_create_route()
    {
        $data = ['name' => 'Test Route', 'origin' => 'A', 'destination' => 'B'];
        $route = Mockery::mock(Route::class);

        $this->repository->shouldReceive('create')
            ->with($data)
            ->once()
            ->andReturn($route);

        $result = $this->service->createRoute($data);

        $this->assertInstanceOf(Route::class, $result);
    }

    public function test_delete_route()
    {
        $route = Mockery::mock(Route::class);

        $this->repository->shouldReceive('delete')
            ->with($route)
            ->once()
            ->andReturn(true);

        $result = $this->service->deleteRoute($route);

        $this->assertTrue($result);
    }
}
