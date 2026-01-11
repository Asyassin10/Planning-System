<?php

namespace Tests\Unit\Repositories;

use App\Models\Resource;
use App\Repositories\ResourceRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResourceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ResourceRepository(new Resource());
    }

    public function test_can_get_all_resources()
    {
        Resource::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_filter_resources_by_type()
    {
        Resource::factory()->count(2)->create(['type' => 'vehicle']);
        Resource::factory()->count(1)->create(['type' => 'driver']);

        $result = $this->repository->all(['type' => 'vehicle']);

        $this->assertCount(2, $result);
    }

    public function test_can_filter_active_resources()
    {
        Resource::factory()->count(2)->create(['is_active' => true]);
        Resource::factory()->count(1)->create(['is_active' => false]);

        $result = $this->repository->all(['active_only' => true]);

        $this->assertCount(2, $result);
    }

    public function test_can_find_resource_by_id()
    {
        $resource = Resource::factory()->create();

        $result = $this->repository->findById($resource->id);

        $this->assertEquals($resource->id, $result->id);
    }

    public function test_can_create_resource()
    {
        $data = [
            'name' => 'Test Resource',
            'type' => 'vehicle',
            'capacity' => 50,
            'is_active' => true,
        ];

        $result = $this->repository->create($data);

        $this->assertDatabaseHas('resources', $data);
        $this->assertEquals('Test Resource', $result->name);
    }

    public function test_can_update_resource()
    {
        $resource = Resource::factory()->create(['name' => 'Old Name']);

        $this->repository->update($resource, ['name' => 'New Name']);

        $this->assertDatabaseHas('resources', [
            'id' => $resource->id,
            'name' => 'New Name',
        ]);
    }

    public function test_can_delete_resource()
    {
        $resource = Resource::factory()->create();

        $this->repository->delete($resource);

        $this->assertDatabaseMissing('resources', ['id' => $resource->id]);
    }
}
