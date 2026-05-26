<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'site_id' => Site::factory(),
            'latitude' => $this->faker->latitude(6, 36),
            'longitude' => $this->faker->longitude(68, 98),
            'distance_from_site' => $this->faker->numberBetween(0, 800),
            'active_power' => $this->faker->randomFloat(2, 1, 500),
            'voltage' => $this->faker->randomFloat(2, 180, 260),
            'current' => $this->faker->randomFloat(2, 1, 80),
            'load_percent' => $this->faker->randomFloat(2, 1, 100),
            'energy_reading' => $this->faker->randomFloat(2, 100, 100000),
            'notes' => $this->faker->optional()->sentence(),
            'suspicious_flag' => false,
            'risk_level' => 'normal',
            'gps_recorded_at' => now(),
            'metadata' => [],
        ];
    }
}
