<?php

namespace Database\Factories;

use App\Models\Experience;
use App\Models\ExperienceParameter;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExperienceParameterFactory extends Factory
{
    protected $model = ExperienceParameter::class;

    public function definition(): array
    {
        return [
            'experience_id' => Experience::factory(),
            'block_name' => $this->faker->randomElement(['Hatch 1', 'Hatch 2', 'Corte', 'Grabado', 'Marcado']),
            'name' => $this->faker->randomElement(['Velocidad', 'Potencia', 'Pasadas', 'LPI', 'DPI', 'Frecuencia', 'Modo', 'Desenfoque']),
            'value' => (string) $this->faker->numberBetween(1, 10000),
            'unit' => $this->faker->randomElement(['mm/min', '%', 'unidad', 'lpi', 'dpi', 'kHz', 'mm', '']),
        ];
    }
}