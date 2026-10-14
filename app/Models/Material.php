<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}