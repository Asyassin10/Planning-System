<?php

namespace Tests\Feature;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ResourceTest extends TestCase
{
    use WithFaker;

    protected function getAuthUser()
    {
        return User::factory()->create();
    }

    protected function getAuthHeaders($user)
    {
        $token = $user->createToken('auth-token')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_can_create_resource()
    {
        $user = $this->getAuthUser();

        $resourceData = [
            'name' => 'Bus 101',
            'type' => 'vehicle',
            'capacity' => 50,
            'is_active' => true,
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->postJson('/api/resources', $resourceData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'type', 'capacity', 'is_active'],
            ]);

        $this->assertDatabaseHas('resources', [
            'name' => 'Bus 101',
            'type' => 'vehicle',
        ]);
    }

    public function test_can_list_all_resources()
    {
        $user = $this->getAuthUser();
        Resource::factory()->count(3)->create();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson('/api/resources');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'type', 'capacity'],
                ],
            ]);
    }

    public function test_can_filter_resources_by_type()
    {
        $user = $this->getAuthUser();
        Resource::factory()->count(2)->create(['type' => 'vehicle']);
        Resource::factory()->count(1)->create(['type' => 'driver']);

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson('/api/resources?type=vehicle');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_can_filter_active_resources()
    {
        $user = $this->getAuthUser();
        Resource::factory()->count(2)->create(['is_active' => true]);
        Resource::factory()->count(1)->create(['is_active' => false]);

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson('/api/resources?active_only=1');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_can_show_single_resource()
    {
        $user = $this->getAuthUser();
        $resource = Resource::factory()->create();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson("/api/resources/{$resource->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['id' => $resource->id],
            ]);
    }

    public function test_can_update_resource()
    {
        $user = $this->getAuthUser();
        $resource = Resource::factory()->create();

        $updateData = [
            'name' => 'Updated Bus',
            'capacity' => 60,
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->putJson("/api/resources/{$resource->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('resources', [
            'id' => $resource->id,
            'name' => 'Updated Bus',
            'capacity' => 60,
        ]);
    }

    public function test_can_delete_resource()
    {
        $user = $this->getAuthUser();
        $resource = Resource::factory()->create();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->deleteJson("/api/resources/{$resource->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('resources', [
            'id' => $resource->id,
        ]);
    }
}
