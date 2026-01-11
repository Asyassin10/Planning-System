<?php

namespace Tests\Feature;

use App\Models\Route;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RouteTest extends TestCase
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

    public function test_can_create_route()
    {
        $user = $this->getAuthUser();

        $routeData = [
            'name' => 'Route A to B',
            'origin' => 'Station A',
            'destination' => 'Station B',
            'distance_km' => 25.5,
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->postJson('/api/routes', $routeData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'origin', 'destination', 'distance_km'],
            ]);

        $this->assertDatabaseHas('routes', [
            'name' => 'Route A to B',
            'origin' => 'Station A',
        ]);
    }

    public function test_can_list_all_routes()
    {
        $user = $this->getAuthUser();
        Route::factory()->count(3)->create();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson('/api/routes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'origin', 'destination'],
                ],
            ]);
    }

    public function test_can_show_single_route()
    {
        $user = $this->getAuthUser();
        $route = Route::factory()->create();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson("/api/routes/{$route->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['id' => $route->id],
            ]);
    }

    public function test_can_update_route()
    {
        $user = $this->getAuthUser();
        $route = Route::factory()->create();

        $updateData = [
            'name' => 'Updated Route',
            'distance_km' => 30.0,
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->putJson("/api/routes/{$route->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('routes', [
            'id' => $route->id,
            'name' => 'Updated Route',
            'distance_km' => 30.0,
        ]);
    }

    public function test_can_delete_route()
    {
        $user = $this->getAuthUser();
        $route = Route::factory()->create();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->deleteJson("/api/routes/{$route->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('routes', [
            'id' => $route->id,
        ]);
    }
}
