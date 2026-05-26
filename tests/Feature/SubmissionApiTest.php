<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SubmissionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_submit_reading_outside_radius_without_rejection(): void
    {
        Role::findOrCreate('Employee');
        $user = User::factory()->create();
        $user->assignRole('Employee');
        $employee = Employee::factory()->create(['user_id' => $user->id, 'email' => $user->email]);
        $site = Site::factory()->create(['latitude' => 28.4595, 'longitude' => 77.0266, 'allowed_radius' => 100]);
        $employee->sites()->sync([$site->id]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/submission/store', [
            'site_id' => $site->id,
            'latitude' => 28.5355,
            'longitude' => 77.3910,
            'active_power' => 42.5,
            'energy_reading' => 1250,
            'notes' => 'All systems normal',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.risk_level', 'suspicious')
            ->assertJsonPath('data.suspicious_flag', true);

        $this->assertDatabaseHas('submissions', [
            'employee_id' => $employee->id,
            'site_id' => $site->id,
            'risk_level' => 'suspicious',
        ]);
    }
}
