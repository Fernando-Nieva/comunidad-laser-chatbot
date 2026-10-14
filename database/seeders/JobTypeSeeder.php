<?php

namespace Database\Seeders;

use App\Models\JobType;
use Illuminate\Database\Seeder;

class JobTypeSeeder extends Seeder
{
    public function run(): void
    {
        $jobTypes = [
            ['name' => 'Grabado', 'active' => true],
            ['name' => 'Corte', 'active' => true],
            ['name' => 'Marcado', 'active' => true],
            ['name' => 'Grabado y corte', 'active' => true],
        ];

        foreach ($jobTypes as $jobType) {
            JobType::create($jobType);
        }
    }
}