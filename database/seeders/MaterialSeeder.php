<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            ['name' => 'MDF', 'active' => true],
            ['name' => 'Acrílico', 'active' => true],
            ['name' => 'Cuero', 'active' => true],
            ['name' => 'Madera de pino', 'active' => true],
            ['name' => 'Cartón', 'active' => true],
            ['name' => 'Vidrio', 'active' => true],
            ['name' => 'Acero inoxidable (marcado)', 'active' => true],
            ['name' => 'Goma EVA', 'active' => true],
            ['name' => 'Bambú', 'active' => true],
            ['name' => 'Anodizado de aluminio', 'active' => true],
            ['name' => 'Tela', 'active' => true],
        ];

        foreach ($materials as $material) {
            Material::create($material);
        }
    }
}