<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Experience extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'material_id',
        'job_type_id',
        'thickness_mm',
        'machine',
        'machine_power_w',
        'software',
        'description',
        'results',
        'observations',
        'status',
    ];

    protected $casts = [
        'thickness_mm' => 'decimal:2',
        'machine_power_w' => 'integer',
        'status' => 'string',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function jobType()
    {
        return $this->belongsTo(JobType::class);
    }

    public function parameters()
    {
        return $this->hasMany(ExperienceParameter::class)->orderBy('block_name')->orderBy('id');
    }

    public function embedding()
    {
        return $this->hasOne(ExperienceEmbedding::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function getKnowledgeText(): string
    {
        $parts = [];

        $parts[] = "Título: {$this->title}";
        $parts[] = "Material: {$this->material->name}";
        $parts[] = "Tipo de trabajo: {$this->jobType->name}";

        if ($this->thickness_mm) {
            $parts[] = "Espesor: {$this->thickness_mm} mm";
        }

        $parts[] = "Máquina: {$this->machine}";

        if ($this->machine_power_w) {
            $parts[] = "Potencia de la máquina: {$this->machine_power_w} W";
        }

        $parts[] = "Software: {$this->software}";
        $parts[] = "Descripción: {$this->description}";

        if ($this->parameters->isNotEmpty()) {
            $parts[] = "Parámetros:";
            $blocks = $this->parameters->groupBy('block_name');
            foreach ($blocks as $blockName => $params) {
                $parts[] = "  Bloque: {$blockName}";
                foreach ($params as $param) {
                    $unit = $param->unit ? " {$param->unit}" : '';
                    $parts[] = "    {$param->name}: {$param->value}{$unit}";
                }
            }
        }

        $parts[] = "Resultados: {$this->results}";

        if ($this->observations) {
            $parts[] = "Observaciones: {$this->observations}";
        }

        return implode("\n", $parts);
    }
}