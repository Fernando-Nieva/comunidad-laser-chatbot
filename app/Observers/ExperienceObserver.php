<?php

namespace App\Observers;

use App\Models\Experience;

class ExperienceObserver
{
    public function updated(Experience $experience): void
    {
        // Mark embedding as outdated when experience content changes
        if ($experience->wasChanged([
            'title', 'material_id', 'job_type_id', 'thickness_mm',
            'machine', 'machine_power_w', 'software', 'description',
            'results', 'observations',
        ])) {
            $experience->embedding()->delete();
        }
    }

    public function deleted(Experience $experience): void
    {
        // Cascade delete is handled by FK, but we can log
        \Illuminate\Support\Facades\Log::info('Experience deleted, embedding removed', [
            'experience_id' => $experience->id,
        ]);
    }
}