<?php

namespace Database\Factories;

use App\Models\Experience;
use App\Models\JobType;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExperienceFactory extends Factory
{
    protected $model = Experience::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'material_id' => Material::factory(),
            'job_type_id' => JobType::factory(),
            'thickness_mm' => $this->faker->optional()->randomFloat(2, 0.5, 20),
            'machine' => $this->faker->randomElement(['Sculpfun S9', 'Ortur LM2', 'Atomstack A10 Pro', 'TwoTrees TS2']),
            'machine_power_w' => $this->faker->optional()->randomElement([5, 10, 20, 40, 60]),
            'software' => $this->faker->randomElement(['LightBurn', 'LaserGRBL', 'RDWorks']),
            'description' => $this->faker->paragraph(),
            'results' => $this->faker->paragraph(),
            'observations' => $this->faker->optional()->paragraph(),
            'status' => 'approved',
        ];
    }
}