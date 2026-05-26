<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->name();
        $email = $this->faker->unique()->safeEmail();

        return [
            'user_id' => User::factory(),
            'employee_code' => 'EMP-'.$this->faker->unique()->numberBetween(1000, 9999),
            'name' => $name,
            'phone' => $this->faker->numerify('9#########'),
            'email' => $email,
            'status' => 'active',
            'last_seen' => $this->faker->optional()->dateTimeBetween('-1 day'),
        ];
    }
}
