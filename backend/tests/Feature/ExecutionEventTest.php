<?php

namespace Tests\Feature;

use App\Models\ExecutionEvent;
use App\Models\OperationalPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExecutionEventTest extends TestCase
{
    use WithFaker;

    protected function getTeamCUser()
    {
        return User::factory()->create(['role' => 'team_c']);
    }

    protected function getAuthHeaders($user)
    {
        $token = $user->createToken('auth-token')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    protected function createActiveVersion()
    {
        $user = User::factory()->create();
        $operationalPlan = OperationalPlan::factory()->create();
        return $operationalPlan->versions()->create([
            'version' => 1,
            'is_active' => true,
            'valid_from' => now()->subDays(1),
            'valid_to' => now()->addDays(30),
            'created_by' => $user->id,
        ]);
    }

    public function test_team_c_can_record_execution_event()
    {
        $user = $this->getTeamCUser();
        $version = $this->createActiveVersion();

        $eventData = [
            'operational_plan_version_id' => $version->id,
            'event_type' => 'departure',
            'event_data' => [
                'location' => 'Station A',
                'timestamp' => now()->toIso8601String(),
            ],
            'recorded_at' => now()->toDateTimeString(),
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->postJson('/api/execution-events', $eventData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'operational_plan_version_id', 'event_type', 'recorded_by'],
            ]);

        $this->assertDatabaseHas('execution_events', [
            'operational_plan_version_id' => $version->id,
            'event_type' => 'departure',
            'recorded_by' => $user->id,
        ]);
    }

    public function test_can_list_all_execution_events()
    {
        $user = $this->getTeamCUser();
        ExecutionEvent::factory()->count(3)->create();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson('/api/execution-events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'operational_plan_version_id', 'event_type', 'recorded_by'],
                ],
            ]);
    }

    public function test_can_filter_execution_events_by_version()
    {
        $user = $this->getTeamCUser();
        $version = $this->createActiveVersion();
        ExecutionEvent::factory()->count(2)->create([
            'operational_plan_version_id' => $version->id,
        ]);
        ExecutionEvent::factory()->count(1)->create();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson("/api/execution-events?operational_plan_version_id={$version->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_can_filter_execution_events_by_type()
    {
        $user = $this->getTeamCUser();
        ExecutionEvent::factory()->count(2)->create(['event_type' => 'departure']);
        ExecutionEvent::factory()->count(1)->create(['event_type' => 'arrival']);

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson('/api/execution-events?event_type=departure');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_can_show_single_execution_event()
    {
        $user = $this->getTeamCUser();
        $executionEvent = ExecutionEvent::factory()->create();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->getJson("/api/execution-events/{$executionEvent->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['id' => $executionEvent->id],
            ]);
    }
}
