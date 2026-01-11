<?php

namespace Tests\Unit\Services;

use App\Models\Resource;
use App\Repositories\ResourceRepository;
use App\Services\ResourceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ResourceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ResourceRepository::class);
        $this->service = new ResourceService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_resources()
    {
        $this->repository->shouldReceive('all')
            ->with([])
            ->once()
            ->andReturn(collect([]));

        $result = $this->service->getAllResources();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_resource_by_id()
    {
        $resource = Mockery::mock(Resource::class);

        $this->repository->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($resource);

        $result = $this->service->getResourceById(1);

        $this->assertInstanceOf(Resource::class, $result);
    }

    public function test_create_resource()
    {
        $data = ['name' => 'Test Resource', 'type' => 'vehicle'];
        $resource = Mockery::mock(Resource::class);

        $this->repository->shouldReceive('create')
            ->with($data)
            ->once()
            ->andReturn($resource);

        $result = $this->service->createResource($data);

        $this->assertInstanceOf(Resource::class, $result);
    }

    public function test_delete_resource()
    {
        $resource = Mockery::mock(Resource::class);

        $this->repository->shouldReceive('delete')
            ->with($resource)
            ->once()
            ->andReturn(true);

        $result = $this->service->deleteResource($resource);

        $this->assertTrue($result);
    }
}
