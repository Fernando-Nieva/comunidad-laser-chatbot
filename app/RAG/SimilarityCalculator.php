<?php

namespace App\RAG;

use InvalidArgumentException;

class SimilarityCalculator
{
    public static function cosineSimilarity(array $vecA, array $vecB): float
    {
        if (count($vecA) !== count($vecB)) {
            throw new InvalidArgumentException(
                'Vectors must have the same dimensions. ' .
                'Got ' . count($vecA) . ' and ' . count($vecB)
            );
        }

        if (count($vecA) === 0) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($vecA as $i => $valA) {
            $valB = $vecB[$i];
            $dotProduct += $valA * $valB;
            $normA += $valA * $valA;
            $normB += $valB * $valB;
        }

        $normA = sqrt($normA);
        $normB = sqrt($normB);

        if ($normA === 0.0 || $normB === 0.0) {
            return 0.0;
        }

        return $dotProduct / ($normA * $normB);
    }
}