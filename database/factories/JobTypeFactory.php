<?php

namespace Database\Factories;

use App\Models\JobType;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobTypeFactory extends Factory
{
    protected $model = JobType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'active' => true,
        ];
    }
}