<?php

namespace Tests\Unit\Services;

use App\Models\PlanningRequest;
use App\Repositories\PlanningRequestRepository;
use App\Services\PlanningRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PlanningRequestServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(PlanningRequestRepository::class);
        $this->service = new PlanningRequestService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_planning_requests()
    {
        $this->repository->shouldReceive('all')
            ->once()
            ->andReturn(collect([]));

        $result = $this->service->getAllPlanningRequests();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_submitted_requests()
    {
        $this->repository->shouldReceive('findByStatus')
            ->with('submitted')
            ->once()
            ->andReturn(collect([]));

        $result = $this->service->getSubmittedRequests();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_draft_requests()
    {
        $this->repository->shouldReceive('findByStatus')
            ->with('draft')
            ->once()
            ->andReturn(collect([]));

        $result = $this->service->getDraftRequests();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_throws_exception_when_updating_submitted_request()
    {
        $planningRequest = Mockery::mock(PlanningRequest::class);
        $planningRequest->shouldReceive('isSubmitted')->andReturn(true);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot update a submitted planning request');

        $this->service->updatePlanningRequest($planningRequest, []);
    }

    public function test_throws_exception_when_deleting_submitted_request()
    {
        $planningRequest = Mockery::mock(PlanningRequest::class);
        $planningRequest->shouldReceive('isSubmitted')->andReturn(true);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot delete a submitted planning request');

        $this->service->deletePlanningRequest($planningRequest);
    }

    public function test_throws_exception_when_submitting_already_submitted_request()
    {
        $planningRequest = Mockery::mock(PlanningRequest::class);
        $planningRequest->shouldReceive('isSubmitted')->andReturn(true);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Planning request is already submitted');

        $this->service->submitPlanningRequest($planningRequest);
    }

    public function test_throws_exception_when_submitting_request_without_items()
    {
        $planningRequest = Mockery::mock(PlanningRequest::class);
        $planningRequest->shouldReceive('isSubmitted')->andReturn(false);

        $this->repository->shouldReceive('getItemCount')
            ->with($planningRequest)
            ->andReturn(0);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot submit a planning request with no items');

        $this->service->submitPlanningRequest($planningRequest);
    }
}
