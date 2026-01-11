<?php

namespace Tests\Unit\Services;

use App\Models\OperationalPlan;
use App\Repositories\OperationalPlanRepository;
use App\Services\OperationalPlanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OperationalPlanServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(OperationalPlanRepository::class);
        $this->service = new OperationalPlanService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_operational_plans()
    {
        $this->repository->shouldReceive('all')
            ->once()
            ->andReturn(collect([]));

        $result = $this->service->getAllOperationalPlans();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_active_plans()
    {
        $this->repository->shouldReceive('getActivePlans')
            ->once()
            ->andReturn(collect([]));

        $result = $this->service->getActivePlans();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_operational_plan_by_id()
    {
        $plan = Mockery::mock(OperationalPlan::class);

        $this->repository->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($plan);

        $result = $this->service->getOperationalPlanById(1);

        $this->assertInstanceOf(OperationalPlan::class, $result);
    }
}
