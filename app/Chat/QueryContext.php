<?php

namespace App\Chat;

class QueryContext
{
    public function __construct(
        public readonly ?string $material = null,
        public readonly ?float $thicknessMm = null,
        public readonly ?int $machinePowerW = null,
        public readonly ?string $jobType = null,
        public readonly ?string $machine = null,
        public readonly ?string $software = null,
    ) {}

    public function toArray(): array
    {
        return [
            'material' => $this->material,
            'thickness_mm' => $this->thicknessMm,
            'machine_power_w' => $this->machinePowerW,
            'job_type' => $this->jobType,
            'machine' => $this->machine,
            'software' => $this->software,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            material: $data['material'] ?? null,
            thicknessMm: isset($data['thickness_mm']) ? (float) $data['thickness_mm'] : null,
            machinePowerW: isset($data['machine_power_w']) ? (int) $data['machine_power_w'] : null,
            jobType: $data['job_type'] ?? null,
            machine: $data['machine'] ?? null,
            software: $data['software'] ?? null,
        );
    }

    public function merge(QueryContext $other): self
    {
        return new self(
            material: $other->material ?? $this->material,
            thicknessMm: $other->thicknessMm ?? $this->thicknessMm,
            machinePowerW: $other->machinePowerW ?? $this->machinePowerW,
            jobType: $other->jobType ?? $this->jobType,
            machine: $other->machine ?? $this->machine,
            software: $other->software ?? $this->software,
        );
    }

    public function isEmpty(): bool
    {
        return $this->material === null
            && $this->thicknessMm === null
            && $this->machinePowerW === null
            && $this->jobType === null
            && $this->machine === null
            && $this->software === null;
    }
}