<?php

namespace App\Search;

interface WebSearchProvider
{
    public function search(string $query, int $limit = 5): array;

    public function isAvailable(): bool;
}