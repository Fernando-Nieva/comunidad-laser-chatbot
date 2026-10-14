<?php

namespace App\AI\Contracts;

interface AIProvider
{
    public function chat(array $messages, array $options = []): string;

    public function embed(string $text): array;

    public function isAvailable(): bool;
}