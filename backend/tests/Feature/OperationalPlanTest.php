<?php

namespace Tests\Feature;

use App\Models\OperationalPlan;
use App\Models\PlanningRequest;
use App\Models\PlanningRequestItem;
use App\Models\Resource;
use App\Models\Route;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OperationalPlanTest extends TestCase
{
    use WithFaker;

    protected function getTeamBUser()
    {
        return User::factory()->create(['role' => 'team_b']);
    }

    protected function getAuthHeaders($user)
    {
        $token = $user->createToken('auth-token')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    protected function createPlanningRequestItem()
    {
        $route = Route::factory()->create();
        $planningRequest = PlanningRequest::factory()->create(['status' => 'submitted']);
        return PlanningRequestItem::factory()->create([
            'planning_request_id' => $planningRequest->id,
            'route_id' => $route->id,
        ]);
    }

    public function test_team_b_can_create_operational_plan()
    {
        $user = $this->getTeamBUser();
        $item = $this->createPlanningRequestItem();
        $resource = Resource::factory()->create();

        $planData = [
            'planning_request_item_id' => $item->id,
            'version' => [
                'valid_from' => now()->addDays(1)->format('Y-m-d'),
                'valid_to' => now()->addDays(30)->format('Y-m-d'),
                'notes' => 'Initial version',
                'resources' => [
                    [
                        'resource_id' => $resource->id,
                        'capacity' => 50,
                        'is_permanent' => true,
                    ],
                ],
            ],
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->postJson('/api/operational-plans', $planData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'planning_request_item_id', 'created_by'],
            ]);

        $this->assertDatabaseHas('operational_plans', [
            'planning_request_item_id' => $item->id,
            'created_by' => $user->id,
        ]);
    }

    public function test_can_list_all_operational_plans()
    {
        $user = $this->getTeamBUser();
        OperationalPlan::factory()->count(3)->create();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson('/api/operational-plans');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'planning_request_item_id', 'created_by'],
                ],
            ]);
    }

    public function test_can_show_single_operational_plan()
    {
        $user = $this->getTeamBUser();
        $operationalPlan = OperationalPlan::factory()->create();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson("/api/operational-plans/{$operationalPlan->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['id' => $operationalPlan->id],
            ]);
    }

    public function test_can_create_new_version()
    {
        $user = $this->getTeamBUser();
        $operationalPlan = OperationalPlan::factory()->create();
        $resource = Resource::factory()->create();

        $versionData = [
            'valid_from' => now()->addDays(31)->format('Y-m-d'),
            'valid_to' => now()->addDays(60)->format('Y-m-d'),
            'notes' => 'Version 2',
            'is_active' => false,
            'resources' => [
                [
                    'resource_id' => $resource->id,
                    'capacity' => 75,
                    'is_permanent' => true,
                ],
            ],
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->postJson("/api/operational-plans/{$operationalPlan->id}/versions", $versionData);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('operational_plan_versions', [
            'operational_plan_id' => $operationalPlan->id,
            'version' => 2,
        ]);
    }

    public function test_can_activate_version()
    {
        $user = $this->getTeamBUser();
        $operationalPlan = OperationalPlan::factory()->create();
        $version = $operationalPlan->versions()->create([
            'version' => 1,
            'is_active' => false,
            'valid_from' => now()->addDays(1),
            'valid_to' => now()->addDays(30),
            'created_by' => $user->id,
        ]);

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->postJson("/api/operational-plan-versions/{$version->id}/activate");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('operational_plan_versions', [
            'id' => $version->id,
            'is_active' => true,
        ]);
    }

    public function test_can_get_active_plans()
    {
        $user = $this->getTeamBUser();
        $operationalPlan = OperationalPlan::factory()->create();
        $operationalPlan->versions()->create([
            'version' => 1,
            'is_active' => true,
            'valid_from' => now()->addDays(1),
            'valid_to' => now()->addDays(30),
            'created_by' => $user->id,
        ]);

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson('/api/operational-plans/active');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }
}
