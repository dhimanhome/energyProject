<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Admin', 'Supervisor', 'Employee'] as $role) {
            Role::findOrCreate($role);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@poweraudit.local'],
            ['name' => 'System Admin', 'phone' => '9999999999', 'password' => 'password', 'status' => 'active']
        );
        $admin->assignRole('Admin');

        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@poweraudit.local'],
            ['name' => 'Field Supervisor', 'phone' => '9999999998', 'password' => 'password', 'status' => 'active']
        );
        $supervisor->assignRole('Supervisor');

        $sites = collect([
            ['site_code' => 'GOV-SOL-001', 'site_name' => 'District Solar Plant', 'latitude' => 28.4595, 'longitude' => 77.0266, 'allowed_radius' => 100, 'address' => 'Sector 29, Gurugram', 'status' => 'active'],
            ['site_code' => 'GOV-PWR-002', 'site_name' => 'Water Works Feeder', 'latitude' => 28.5355, 'longitude' => 77.3910, 'allowed_radius' => 120, 'address' => 'Noida government utility block', 'status' => 'active'],
            ['site_code' => 'GOV-SOL-003', 'site_name' => 'Rooftop Solar School', 'latitude' => 28.6139, 'longitude' => 77.2090, 'allowed_radius' => 80, 'address' => 'Central Delhi school campus', 'status' => 'active'],
        ])->map(fn (array $site) => Site::firstOrCreate(['site_code' => $site['site_code']], $site));

        foreach (range(1, 4) as $index) {
            $user = User::firstOrCreate(
                ['email' => "employee{$index}@poweraudit.local"],
                ['name' => "Field Employee {$index}", 'phone' => '900000000'.$index, 'password' => 'password', 'status' => 'active']
            );
            $user->assignRole('Employee');

            $employee = Employee::firstOrCreate(
                ['employee_code' => 'EMP-00'.$index],
                ['user_id' => $user->id, 'name' => $user->name, 'phone' => $user->phone, 'email' => $user->email, 'status' => 'active', 'last_seen' => now()->subMinutes($index * 4)]
            );
            $employee->sites()->sync($sites->pluck('id')->shuffle()->take(2)->all());
        }

        Employee::with('sites')->get()->each(function (Employee $employee): void {
            $site = $employee->sites->first();

            if (! $site) {
                return;
            }

            $employee->submissions()->firstOrCreate([
                'site_id' => $site->id,
                'energy_reading' => 1200 + $employee->id,
            ], [
                'latitude' => (float) $site->latitude + (0.0001 * $employee->id),
                'longitude' => (float) $site->longitude + (0.0001 * $employee->id),
                'distance_from_site' => 20 * $employee->id,
                'active_power' => 40 + $employee->id,
                'voltage' => 228 + $employee->id,
                'current' => 10 + $employee->id,
                'load_percent' => 60 + $employee->id,
                'notes' => 'Seeded normal reading',
                'risk_level' => 'normal',
                'gps_recorded_at' => now(),
                'metadata' => ['seeded' => true],
            ]);
        });
    }
}
