<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['uuid'];

    protected $casts = [
        'uuid' => 'string',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}