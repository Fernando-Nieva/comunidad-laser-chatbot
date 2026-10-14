<?php

namespace Database\Factories;

use App\Models\Experience;
use App\Models\ExperienceEmbedding;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExperienceEmbeddingFactory extends Factory
{
    protected $model = ExperienceEmbedding::class;

    public function definition(): array
    {
        $dimensions = 1024;
        $embedding = [];
        for ($i = 0; $i < $dimensions; $i++) {
            $embedding[] = $this->faker->randomFloat(6, -1, 1);
        }

        return [
            'experience_id' => Experience::factory(),
            'embedding' => $embedding,
            'source_text' => $this->faker->paragraph(3),
            'model' => 'bge-m3',
        ];
    }
}