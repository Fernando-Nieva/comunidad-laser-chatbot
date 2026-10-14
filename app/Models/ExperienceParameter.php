<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExperienceParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'experience_id',
        'block_name',
        'name',
        'value',
        'unit',
    ];

    protected $casts = [
        'value' => 'string',
    ];

    public function experience()
    {
        return $this->belongsTo(Experience::class);
    }
}