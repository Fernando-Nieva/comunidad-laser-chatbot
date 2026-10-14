<?php

use App\Models\Experience;
use App\Models\ExperienceParameter;
use App\Models\JobType;
use App\Models\Material;
use App\Models\ExperienceEmbedding;

test('material has many experiences', function () {
    $material = Material::factory()->create();
    Experience::factory()->count(3)->create(['material_id' => $material->id]);

    expect($material->experiences)->toHaveCount(3);
    expect($material->experiences->first())->toBeInstanceOf(Experience::class);
});

test('job type has many experiences', function () {
    $jobType = JobType::factory()->create();
    Experience::factory()->count(2)->create(['job_type_id' => $jobType->id]);

    expect($jobType->experiences)->toHaveCount(2);
    expect($jobType->experiences->first())->toBeInstanceOf(Experience::class);
});

test('experience belongs to material', function () {
    $material = Material::factory()->create();
    $experience = Experience::factory()->create(['material_id' => $material->id]);

    expect($experience->material)->toBeInstanceOf(Material::class);
    expect($experience->material->id)->toBe($material->id);
});

test('experience belongs to job type', function () {
    $jobType = JobType::factory()->create();
    $experience = Experience::factory()->create(['job_type_id' => $jobType->id]);

    expect($experience->jobType)->toBeInstanceOf(JobType::class);
    expect($experience->jobType->id)->toBe($jobType->id);
});

test('experience has many parameters', function () {
    $experience = Experience::factory()->create();
    ExperienceParameter::factory()->count(4)->create(['experience_id' => $experience->id]);

    expect($experience->parameters)->toHaveCount(4);
    expect($experience->parameters->first())->toBeInstanceOf(ExperienceParameter::class);
});

test('experience parameters ordered by block_name and id', function () {
    $experience = Experience::factory()->create();

    ExperienceParameter::factory()->create([
        'experience_id' => $experience->id,
        'block_name' => 'Hatch 2',
        'name' => 'Potencia',
    ]);
    ExperienceParameter::factory()->create([
        'experience_id' => $experience->id,
        'block_name' => 'Hatch 1',
        'name' => 'Velocidad',
    ]);
    ExperienceParameter::factory()->create([
        'experience_id' => $experience->id,
        'block_name' => 'Hatch 1',
        'name' => 'Potencia',
    ]);

    $params = $experience->parameters;
    expect($params[0]->block_name)->toBe('Hatch 1');
    expect($params[0]->name)->toBe('Velocidad');
    expect($params[1]->block_name)->toBe('Hatch 1');
    expect($params[1]->name)->toBe('Potencia');
    expect($params[2]->block_name)->toBe('Hatch 2');
});

test('experience has one embedding', function () {
    $experience = Experience::factory()->create();
    $embedding = ExperienceEmbedding::factory()->create(['experience_id' => $experience->id]);

    expect($experience->embedding)->toBeInstanceOf(ExperienceEmbedding::class);
    expect($experience->embedding->id)->toBe($embedding->id);
});

test('experience embedding casts embedding to array', function () {
    $experience = Experience::factory()->create();
    $embedding = ExperienceEmbedding::factory()->create([
        'experience_id' => $experience->id,
        'embedding' => [0.1, 0.2, 0.3],
    ]);

    expect($embedding->embedding)->toBeArray();
    expect($embedding->embedding)->toBe([0.1, 0.2, 0.3]);
});

test('experience parameter belongs to experience', function () {
    $experience = Experience::factory()->create();
    $param = ExperienceParameter::factory()->create(['experience_id' => $experience->id]);

    expect($param->experience)->toBeInstanceOf(Experience::class);
    expect($param->experience->id)->toBe($experience->id);
});

test('experience embedding belongs to experience', function () {
    $experience = Experience::factory()->create();
    $embedding = ExperienceEmbedding::factory()->create(['experience_id' => $experience->id]);

    expect($embedding->experience)->toBeInstanceOf(Experience::class);
    expect($embedding->experience->id)->toBe($experience->id);
});

test('experience scopes: approved only returns approved', function () {
    Experience::factory()->create(['status' => 'approved']);
    Experience::factory()->create(['status' => 'pending']);
    Experience::factory()->create(['status' => 'rejected']);

    $approved = Experience::approved()->get();

    expect($approved)->toHaveCount(1);
    expect($approved->first()->status)->toBe('approved');
});

test('material scope active', function () {
    Material::factory()->create(['active' => true]);
    Material::factory()->create(['active' => false]);

    $active = Material::active()->get();

    expect($active)->toHaveCount(1);
    expect($active->first()->active)->toBeTrue();
});

test('job type scope active', function () {
    JobType::factory()->create(['active' => true]);
    JobType::factory()->create(['active' => false]);

    $active = JobType::active()->get();

    expect($active)->toHaveCount(1);
    expect($active->first()->active)->toBeTrue();
});