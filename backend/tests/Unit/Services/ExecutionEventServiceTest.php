<?php

namespace Tests\Unit\Services;

use App\Models\ExecutionEvent;
use App\Repositories\ExecutionEventRepository;
use App\Services\ExecutionEventService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ExecutionEventServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ExecutionEventRepository::class);
        $this->service = new ExecutionEventService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_execution_events()
    {
        $this->repository->shouldReceive('all')
            ->with([])
            ->once()
            ->andReturn(collect([]));

        $result = $this->service->getAllExecutionEvents();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_all_execution_events_with_filters()
    {
        $filters = ['event_type' => 'departure'];

        $this->repository->shouldReceive('all')
            ->with($filters)
            ->once()
            ->andReturn(collect([]));

        $result = $this->service->getAllExecutionEvents($filters);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_execution_event_by_id()
    {
        $event = Mockery::mock(ExecutionEvent::class);

        $this->repository->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($event);

        $result = $this->service->getExecutionEventById(1);

        $this->assertInstanceOf(ExecutionEvent::class, $result);
    }
}
