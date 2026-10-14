<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExperienceEmbedding extends Model
{
    use HasFactory;

    protected $fillable = [
        'experience_id',
        'embedding',
        'source_text',
        'model',
    ];

    protected $casts = [
        'embedding' => 'array',
    ];

    public function experience()
    {
        return $this->belongsTo(Experience::class);
    }
}