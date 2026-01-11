<?php

namespace Tests\Unit\Repositories;

use App\Models\Route;
use App\Repositories\RouteRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new RouteRepository(new Route());
    }

    public function test_can_get_all_routes()
    {
        Route::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_find_route_by_id()
    {
        $route = Route::factory()->create();

        $result = $this->repository->findById($route->id);

        $this->assertEquals($route->id, $result->id);
    }

    public function test_can_create_route()
    {
        $data = [
            'name' => 'Route A-B',
            'origin' => 'Station A',
            'destination' => 'Station B',
            'distance_km' => 25.5,
        ];

        $result = $this->repository->create($data);

        $this->assertDatabaseHas('routes', $data);
        $this->assertEquals('Route A-B', $result->name);
    }

    public function test_can_update_route()
    {
        $route = Route::factory()->create(['name' => 'Old Route']);

        $this->repository->update($route, ['name' => 'New Route']);

        $this->assertDatabaseHas('routes', [
            'id' => $route->id,
            'name' => 'New Route',
        ]);
    }

    public function test_can_delete_route()
    {
        $route = Route::factory()->create();

        $this->repository->delete($route);

        $this->assertDatabaseMissing('routes', ['id' => $route->id]);
    }
}
