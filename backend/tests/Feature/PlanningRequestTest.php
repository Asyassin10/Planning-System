<?php

namespace Tests\Feature;

use App\Models\PlanningRequest;
use App\Models\Route;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlanningRequestTest extends TestCase
{
    use WithFaker;

    protected function getTeamAUser()
    {
        return User::factory()->create(['role' => 'team_a']);
    }

    protected function getAuthHeaders($user)
    {
        $token = $user->createToken('auth-token')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_team_a_can_create_planning_request()
    {
        $user = $this->getTeamAUser();
        $route = Route::factory()->create();

        $requestData = [
            'items' => [
                [
                    'route_id' => $route->id,
                    'capacity' => 100,
                    'start_date' => now()->addDays(1)->format('Y-m-d'),
                    'end_date' => now()->addDays(10)->format('Y-m-d'),
                ],
            ],
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->postJson('/api/planning-requests', $requestData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'status', 'created_by'],
            ]);

        $this->assertDatabaseHas('planning_requests', [
            'created_by' => $user->id,
            'status' => 'draft',
        ]);
    }

    public function test_can_list_all_planning_requests()
    {
        $user = $this->getTeamAUser();
        PlanningRequest::factory()->count(3)->create();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson('/api/planning-requests');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'status', 'created_by'],
                ],
            ]);
    }

    public function test_can_get_submitted_planning_requests()
    {
        $user = $this->getTeamAUser();
        PlanningRequest::factory()->count(2)->create(['status' => 'submitted']);
        PlanningRequest::factory()->count(1)->create(['status' => 'draft']);

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson('/api/planning-requests/submitted');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_can_get_draft_planning_requests()
    {
        $user = $this->getTeamAUser();
        PlanningRequest::factory()->count(2)->create(['status' => 'draft']);
        PlanningRequest::factory()->count(1)->create(['status' => 'submitted']);

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson('/api/planning-requests/draft');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_can_show_single_planning_request()
    {
        $user = $this->getTeamAUser();
        $planningRequest = PlanningRequest::factory()->create();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson("/api/planning-requests/{$planningRequest->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['id' => $planningRequest->id],
            ]);
    }

    public function test_can_update_draft_planning_request()
    {
        $user = $this->getTeamAUser();
        $route = Route::factory()->create();
        $planningRequest = PlanningRequest::factory()->create([
            'status' => 'draft',
            'created_by' => $user->id,
        ]);

        $updateData = [
            'items' => [
                [
                    'route_id' => $route->id,
                    'capacity' => 200,
                    'start_date' => now()->addDays(2)->format('Y-m-d'),
                    'end_date' => now()->addDays(12)->format('Y-m-d'),
                ],
            ],
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->putJson("/api/planning-requests/{$planningRequest->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_cannot_update_submitted_planning_request()
    {
        $user = $this->getTeamAUser();
        $route = Route::factory()->create();
        $planningRequest = PlanningRequest::factory()->create([
            'status' => 'submitted',
            'created_by' => $user->id,
        ]);

        $updateData = [
            'items' => [
                [
                    'route_id' => $route->id,
                    'capacity' => 200,
                    'start_date' => now()->addDays(2)->format('Y-m-d'),
                    'end_date' => now()->addDays(12)->format('Y-m-d'),
                ],
            ],
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->putJson("/api/planning-requests/{$planningRequest->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_can_delete_draft_planning_request()
    {
        $user = $this->getTeamAUser();
        $planningRequest = PlanningRequest::factory()->create([
            'status' => 'draft',
            'created_by' => $user->id,
        ]);

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->deleteJson("/api/planning-requests/{$planningRequest->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('planning_requests', [
            'id' => $planningRequest->id,
        ]);
    }

    public function test_cannot_delete_submitted_planning_request()
    {
        $user = $this->getTeamAUser();
        $planningRequest = PlanningRequest::factory()->create([
            'status' => 'submitted',
            'created_by' => $user->id,
        ]);

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->deleteJson("/api/planning-requests/{$planningRequest->id}");

        $response->assertStatus(403);
    }

    public function test_can_submit_planning_request_with_items()
    {
        $user = $this->getTeamAUser();
        $route = Route::factory()->create();
        $planningRequest = PlanningRequest::factory()->create([
            'status' => 'draft',
            'created_by' => $user->id,
        ]);
        $planningRequest->items()->create([
            'route_id' => $route->id,
            'capacity' => 100,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(10),
        ]);

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->postJson("/api/planning-requests/{$planningRequest->id}/submit");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('planning_requests', [
            'id' => $planningRequest->id,
            'status' => 'submitted',
        ]);
    }

    public function test_cannot_submit_planning_request_without_items()
    {
        $user = $this->getTeamAUser();
        $planningRequest = PlanningRequest::factory()->create([
            'status' => 'draft',
            'created_by' => $user->id,
        ]);

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->postJson("/api/planning-requests/{$planningRequest->id}/submit");

        $response->assertStatus(400);
    }
}
