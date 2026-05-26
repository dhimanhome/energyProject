<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SiteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'site_code' => 'SITE-'.$this->faker->unique()->numberBetween(1000, 9999),
            'site_name' => $this->faker->company().' Power Site',
            'latitude' => $this->faker->latitude(6, 36),
            'longitude' => $this->faker->longitude(68, 98),
            'allowed_radius' => $this->faker->randomElement([80, 100, 150, 200]),
            'address' => $this->faker->address(),
            'status' => 'active',
        ];
    }
}
