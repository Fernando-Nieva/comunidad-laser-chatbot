<?php

use App\Models\Experience;
use App\Models\ExperienceParameter;
use App\Models\JobType;
use App\Models\Material;

test('getKnowledgeText includes title, material, job type', function () {
    $material = Material::factory()->create(['name' => 'MDF']);
    $jobType = JobType::factory()->create(['name' => 'Grabado']);
    $experience = Experience::factory()->create([
        'title' => 'Grabado prueba',
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
        'description' => 'Descripción de prueba',
        'results' => 'Resultados de prueba',
    ]);

    $text = $experience->getKnowledgeText();

    expect($text)->toContain('Título: Grabado prueba');
    expect($text)->toContain('Material: MDF');
    expect($text)->toContain('Tipo de trabajo: Grabado');
    expect($text)->toContain('Descripción: Descripción de prueba');
    expect($text)->toContain('Resultados: Resultados de prueba');
});

test('getKnowledgeText includes thickness when present', function () {
    $material = Material::factory()->create(['name' => 'Acrílico']);
    $jobType = JobType::factory()->create(['name' => 'Corte']);
    $experience = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
        'thickness_mm' => 3.5,
    ]);

    $text = $experience->getKnowledgeText();

    expect($text)->toContain('Espesor: 3.50 mm');
});

test('getKnowledgeText excludes thickness when null', function () {
    $material = Material::factory()->create(['name' => 'Vidrio']);
    $jobType = JobType::factory()->create(['name' => 'Grabado']);
    $experience = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
        'thickness_mm' => null,
    ]);

    $text = $experience->getKnowledgeText();

    expect($text)->not->toContain('Espesor:');
});

test('getKnowledgeText includes machine and machine power', function () {
    $material = Material::factory()->create(['name' => 'Madera de pino']);
    $jobType = JobType::factory()->create(['name' => 'Corte']);
    $experience = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
        'machine' => 'Sculpfun S9',
        'machine_power_w' => 10,
    ]);

    $text = $experience->getKnowledgeText();

    expect($text)->toContain('Máquina: Sculpfun S9');
    expect($text)->toContain('Potencia de la máquina: 10 W');
});

test('getKnowledgeText includes machine power only when present', function () {
    $material = Material::factory()->create(['name' => 'Cuero']);
    $jobType = JobType::factory()->create(['name' => 'Grabado']);
    $experience = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
        'machine' => 'Ortur LM2',
        'machine_power_w' => null,
    ]);

    $text = $experience->getKnowledgeText();

    expect($text)->toContain('Máquina: Ortur LM2');
    expect($text)->not->toContain('Potencia de la máquina:');
});

test('getKnowledgeText includes software', function () {
    $material = Material::factory()->create(['name' => 'Cartón']);
    $jobType = JobType::factory()->create(['name' => 'Corte']);
    $experience = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
        'software' => 'LightBurn',
    ]);

    $text = $experience->getKnowledgeText();

    expect($text)->toContain('Software: LightBurn');
});

test('getKnowledgeText includes parameters grouped by block', function () {
    $material = Material::factory()->create(['name' => 'MDF']);
    $jobType = JobType::factory()->create(['name' => 'Grabado y corte']);
    $experience = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
    ]);

    ExperienceParameter::factory()->create([
        'experience_id' => $experience->id,
        'block_name' => 'Grabado',
        'name' => 'Velocidad',
        'value' => '3000',
        'unit' => 'mm/min',
    ]);
    ExperienceParameter::factory()->create([
        'experience_id' => $experience->id,
        'block_name' => 'Grabado',
        'name' => 'Potencia',
        'value' => '25',
        'unit' => '%',
    ]);
    ExperienceParameter::factory()->create([
        'experience_id' => $experience->id,
        'block_name' => 'Corte',
        'name' => 'Velocidad',
        'value' => '400',
        'unit' => 'mm/min',
    ]);

    $text = $experience->getKnowledgeText();

    expect($text)->toContain('Parámetros:');
    expect($text)->toContain('Bloque: Grabado');
    expect($text)->toContain('Velocidad: 3000 mm/min');
    expect($text)->toContain('Potencia: 25 %');
    expect($text)->toContain('Bloque: Corte');
    expect($text)->toContain('Velocidad: 400 mm/min');
});

test('getKnowledgeText includes parameters without unit', function () {
    $material = Material::factory()->create(['name' => 'MDF']);
    $jobType = JobType::factory()->create(['name' => 'Grabado']);
    $experience = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
    ]);

    ExperienceParameter::factory()->create([
        'experience_id' => $experience->id,
        'block_name' => 'Hatch 1',
        'name' => 'Modo',
        'value' => 'Escala de grises',
        'unit' => '',
    ]);

    $text = $experience->getKnowledgeText();

    expect($text)->toContain('Modo: Escala de grises');
    expect($text)->not->toContain('Escala de grises ');
});

test('getKnowledgeText includes observations when present', function () {
    $material = Material::factory()->create(['name' => 'Bambú']);
    $jobType = JobType::factory()->create(['name' => 'Grabado']);
    $experience = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
        'observations' => 'Usar cinta de enmascarar',
    ]);

    $text = $experience->getKnowledgeText();

    expect($text)->toContain('Observaciones: Usar cinta de enmascarar');
});

test('getKnowledgeText excludes observations when null', function () {
    $material = Material::factory()->create(['name' => 'Goma EVA']);
    $jobType = JobType::factory()->create(['name' => 'Corte']);
    $experience = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
        'observations' => null,
    ]);

    $text = $experience->getKnowledgeText();

    expect($text)->not->toContain('Observaciones:');
});

test('getKnowledgeText formats complete experience correctly', function () {
    $material = Material::factory()->create(['name' => 'MDF']);
    $jobType = JobType::factory()->create(['name' => 'Grabado']);
    $experience = Experience::factory()->create([
        'title' => 'Grabado foto MDF 3mm',
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
        'thickness_mm' => 3.0,
        'machine' => 'Sculpfun S9',
        'machine_power_w' => 10,
        'software' => 'LightBurn',
        'description' => 'Grabado fotografía escala grises',
        'results' => 'Buen contraste',
        'observations' => 'Aire asistido recomendado',
    ]);

    ExperienceParameter::factory()->create([
        'experience_id' => $experience->id,
        'block_name' => 'Hatch 1',
        'name' => 'Velocidad',
        'value' => '3000',
        'unit' => 'mm/min',
    ]);
    ExperienceParameter::factory()->create([
        'experience_id' => $experience->id,
        'block_name' => 'Hatch 1',
        'name' => 'Potencia',
        'value' => '25',
        'unit' => '%',
    ]);
    ExperienceParameter::factory()->create([
        'experience_id' => $experience->id,
        'block_name' => 'Hatch 1',
        'name' => 'LPI',
        'value' => '300',
        'unit' => 'lpi',
    ]);

    $text = $experience->getKnowledgeText();

    $expectedParts = [
        'Título: Grabado foto MDF 3mm',
        'Material: MDF',
        'Tipo de trabajo: Grabado',
        'Espesor: 3.00 mm',
        'Máquina: Sculpfun S9',
        'Potencia de la máquina: 10 W',
        'Software: LightBurn',
        'Descripción: Grabado fotografía escala grises',
        'Parámetros:',
        'Bloque: Hatch 1',
        'Velocidad: 3000 mm/min',
        'Potencia: 25 %',
        'LPI: 300 lpi',
        'Resultados: Buen contraste',
        'Observaciones: Aire asistido recomendado',
    ];

    foreach ($expectedParts as $part) {
        expect($text)->toContain($part);
    }
});

test('getKnowledgeText returns string with newlines', function () {
    $material = Material::factory()->create(['name' => 'MDF']);
    $jobType = JobType::factory()->create(['name' => 'Grabado']);
    $experience = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
    ]);

    $text = $experience->getKnowledgeText();

    expect($text)->toBeString();
    expect($text)->toContain("\n");
});